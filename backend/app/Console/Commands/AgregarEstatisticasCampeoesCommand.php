<?php

namespace App\Console\Commands;

use App\Models\ChampionItemStat;
use App\Models\ChampionMatchup;
use App\Models\ChampionPositionStat;
use App\Models\ChampionRuneStat;
use App\Models\ChampionStat;
use App\Models\MatchParticipante;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class AgregarEstatisticasCampeoesCommand extends Command
{
    protected $signature = 'lol:agregar-estatisticas-campeoes {--patch=}';

    protected $description = 'Agrega estatisticas de campeoes (geral, posicao, itens, runas e matchups).';

    public function handle(): int
    {
        $patchFiltro = $this->option('patch');

        try {
            $rows = MatchParticipante::query()
                ->join('matches', 'matches.id', '=', 'match_participantes.match_id')
                ->whereNotNull('match_participantes.campeao')
                ->where('match_participantes.campeao', '!=', '')
                ->whereRaw("COALESCE(match_participantes.team_position, match_participantes.lane) IS NOT NULL")
                ->whereRaw("COALESCE(match_participantes.team_position, match_participantes.lane) <> ''");

            if ($patchFiltro) {
                $rows->where('matches.versao', 'like', $patchFiltro.'%');
            }

            $rows = $rows
                ->select([
                    'match_participantes.match_id',
                    'match_participantes.campeao',
                    'match_participantes.team_position',
                    'match_participantes.lane',
                    'match_participantes.venceu',
                    'match_participantes.kills',
                    'match_participantes.deaths',
                    'match_participantes.assists',
                    'match_participantes.gold',
                    'match_participantes.farm',
                    'match_participantes.dano_total',
                    'match_participantes.itens',
                    'match_participantes.runas',
                    'matches.versao',
                ])
                ->get();

            if ($rows->isEmpty()) {
                $this->warn('Nenhum dado elegivel para agregacao.');
                return self::SUCCESS;
            }

            DB::transaction(function () use ($rows): void {
                $patches = $rows->pluck('versao')
                    ->map(fn (string $v) => $this->normalizarPatch($v))
                    ->unique()
                    ->values();

                ChampionPositionStat::query()->whereIn('patch', $patches)->delete();
                ChampionStat::query()->whereIn('patch', $patches)->delete();
                ChampionItemStat::query()->whereIn('patch', $patches)->delete();
                ChampionRuneStat::query()->whereIn('patch', $patches)->delete();
                ChampionMatchup::query()->whereIn('patch', $patches)->delete();

                $totaisPorPatch = [];
                $totaisPorPatchPosicao = [];
                $statsPosicao = [];
                $statsCampeao = [];
                $statsItem = [];
                $statsRuna = [];
                $statsMatchup = [];

                foreach ($rows as $row) {
                    $patch = $this->normalizarPatch((string) $row->versao);
                    $posicao = $this->normalizarPosicao($row->team_position ?? $row->lane ?? null);

                    if ($posicao === null) {
                        continue;
                    }

                    $campeao = (string) $row->campeao;
                    $venceu = (bool) $row->venceu;
                    $totaisPorPatch[$patch] = ($totaisPorPatch[$patch] ?? 0) + 1;
                    $kPatchPosicao = $patch.'|'.$posicao;
                    $totaisPorPatchPosicao[$kPatchPosicao] = ($totaisPorPatchPosicao[$kPatchPosicao] ?? 0) + 1;

                    $kPosicao = $campeao.'|'.$posicao.'|'.$patch;
                    $statsPosicao[$kPosicao]['campeao'] = $campeao;
                    $statsPosicao[$kPosicao]['posicao'] = $posicao;
                    $statsPosicao[$kPosicao]['patch'] = $patch;
                    $statsPosicao[$kPosicao]['partidas'] = ($statsPosicao[$kPosicao]['partidas'] ?? 0) + 1;
                    $statsPosicao[$kPosicao]['vitorias'] = ($statsPosicao[$kPosicao]['vitorias'] ?? 0) + ($venceu ? 1 : 0);

                    $kCampeao = $campeao.'|'.$patch;
                    $statsCampeao[$kCampeao]['campeao'] = $campeao;
                    $statsCampeao[$kCampeao]['patch'] = $patch;
                    $statsCampeao[$kCampeao]['partidas'] = ($statsCampeao[$kCampeao]['partidas'] ?? 0) + 1;
                    $statsCampeao[$kCampeao]['vitorias'] = ($statsCampeao[$kCampeao]['vitorias'] ?? 0) + ($venceu ? 1 : 0);
                    $statsCampeao[$kCampeao]['kills'] = ($statsCampeao[$kCampeao]['kills'] ?? 0) + (int) $row->kills;
                    $statsCampeao[$kCampeao]['deaths'] = ($statsCampeao[$kCampeao]['deaths'] ?? 0) + (int) $row->deaths;
                    $statsCampeao[$kCampeao]['assists'] = ($statsCampeao[$kCampeao]['assists'] ?? 0) + (int) $row->assists;
                    $statsCampeao[$kCampeao]['farm'] = ($statsCampeao[$kCampeao]['farm'] ?? 0) + (int) $row->farm;
                    $statsCampeao[$kCampeao]['gold'] = ($statsCampeao[$kCampeao]['gold'] ?? 0) + (int) $row->gold;
                    $statsCampeao[$kCampeao]['damage'] = ($statsCampeao[$kCampeao]['damage'] ?? 0) + (int) $row->dano_total;

                    $itens = is_array($row->itens) ? $row->itens : [];
                    foreach ($itens as $itemId) {
                        $item = (int) $itemId;
                        if ($item <= 0) {
                            continue;
                        }
                        $kItem = $campeao.'|'.$posicao.'|'.$patch.'|'.$item;
                        $statsItem[$kItem]['campeao'] = $campeao;
                        $statsItem[$kItem]['posicao'] = $posicao;
                        $statsItem[$kItem]['patch'] = $patch;
                        $statsItem[$kItem]['item'] = $item;
                        $statsItem[$kItem]['partidas'] = ($statsItem[$kItem]['partidas'] ?? 0) + 1;
                        $statsItem[$kItem]['vitorias'] = ($statsItem[$kItem]['vitorias'] ?? 0) + ($venceu ? 1 : 0);
                    }

                    $runa = $this->extrairRunaPrimaria($row->runas);
                    if ($runa !== null) {
                        $kRuna = $campeao.'|'.$posicao.'|'.$patch.'|'.$runa;
                        $statsRuna[$kRuna]['campeao'] = $campeao;
                        $statsRuna[$kRuna]['posicao'] = $posicao;
                        $statsRuna[$kRuna]['patch'] = $patch;
                        $statsRuna[$kRuna]['runa'] = (string) $runa;
                        $statsRuna[$kRuna]['partidas'] = ($statsRuna[$kRuna]['partidas'] ?? 0) + 1;
                        $statsRuna[$kRuna]['vitorias'] = ($statsRuna[$kRuna]['vitorias'] ?? 0) + ($venceu ? 1 : 0);
                    }
                }

                $porMatch = $rows->groupBy('match_id');
                foreach ($porMatch as $jogadoresDaMatch) {
                    foreach ($jogadoresDaMatch as $a) {
                        $posicaoA = $this->normalizarPosicao($a->team_position ?? $a->lane ?? null);
                        if ($posicaoA === null) {
                            continue;
                        }
                        foreach ($jogadoresDaMatch as $b) {
                            if ($a === $b || $a->campeao === $b->campeao) {
                                continue;
                            }
                            $posicaoB = $this->normalizarPosicao($b->team_position ?? $b->lane ?? null);
                            if ($posicaoA !== $posicaoB) {
                                continue;
                            }
                            if ((bool) $a->venceu === (bool) $b->venceu) {
                                continue;
                            }

                            $patch = $this->normalizarPatch((string) $a->versao);
                            $k = $a->campeao.'|'.$b->campeao.'|'.$posicaoA.'|'.$patch;
                            $statsMatchup[$k]['campeao'] = (string) $a->campeao;
                            $statsMatchup[$k]['adversario'] = (string) $b->campeao;
                            $statsMatchup[$k]['posicao'] = $posicaoA;
                            $statsMatchup[$k]['patch'] = $patch;
                            $statsMatchup[$k]['partidas'] = ($statsMatchup[$k]['partidas'] ?? 0) + 1;
                            $statsMatchup[$k]['vitorias'] = ($statsMatchup[$k]['vitorias'] ?? 0) + ((bool) $a->venceu ? 1 : 0);
                        }
                    }
                }

                foreach ($statsPosicao as $s) {
                    $partidas = (int) $s['partidas'];
                    $vitorias = (int) $s['vitorias'];
                    $totalPatch = max(1, (int) ($totaisPorPatch[$s['patch']] ?? 1));
                    ChampionPositionStat::create([
                        'campeao' => $s['campeao'],
                        'posicao' => $s['posicao'],
                        'patch' => $s['patch'],
                        'partidas' => $partidas,
                        'vitorias' => $vitorias,
                        'winrate' => round(($vitorias / max(1, $partidas)) * 100, 2),
                        'pickrate' => round(($partidas / $totalPatch) * 100, 2),
                    ]);
                }

                foreach ($statsCampeao as $s) {
                    $partidas = (int) $s['partidas'];
                    $vitorias = (int) $s['vitorias'];
                    $deaths = (int) $s['deaths'];
                    $totalPatch = max(1, (int) ($totaisPorPatch[$s['patch']] ?? 1));
                    ChampionStat::create([
                        'campeao' => $s['campeao'],
                        'patch' => $s['patch'],
                        'partidas' => $partidas,
                        'vitorias' => $vitorias,
                        'winrate' => round(($vitorias / max(1, $partidas)) * 100, 2),
                        'pickrate' => round(($partidas / $totalPatch) * 100, 2),
                        'kda' => round(((int) $s['kills'] + (int) $s['assists']) / max(1, $deaths), 2),
                        'kills_medias' => round(((int) $s['kills']) / max(1, $partidas), 2),
                        'deaths_medias' => round($deaths / max(1, $partidas), 2),
                        'assists_medias' => round(((int) $s['assists']) / max(1, $partidas), 2),
                        'farm_medio' => round(((int) $s['farm']) / max(1, $partidas), 2),
                        'gold_medio' => round(((int) $s['gold']) / max(1, $partidas), 2),
                        'damage_medio' => round(((int) $s['damage']) / max(1, $partidas), 2),
                    ]);
                }

                foreach ($statsItem as $s) {
                    $partidas = (int) $s['partidas'];
                    $vitorias = (int) $s['vitorias'];
                    $totalPatchPosicao = max(1, (int) ($totaisPorPatchPosicao[$s['patch'].'|'.$s['posicao']] ?? 1));
                    ChampionItemStat::create([
                        'campeao' => $s['campeao'],
                        'posicao' => $s['posicao'],
                        'patch' => $s['patch'],
                        'item' => $s['item'],
                        'itens' => [$s['item']],
                        'partidas' => $partidas,
                        'vitorias' => $vitorias,
                        'winrate' => round(($vitorias / max(1, $partidas)) * 100, 2),
                        'pickrate' => round(($partidas / $totalPatchPosicao) * 100, 2),
                    ]);
                }

                foreach ($statsRuna as $s) {
                    $partidas = (int) $s['partidas'];
                    $vitorias = (int) $s['vitorias'];
                    ChampionRuneStat::create([
                        'campeao' => $s['campeao'],
                        'posicao' => $s['posicao'],
                        'patch' => $s['patch'],
                        'runa' => $s['runa'],
                        'runas' => ['primaria' => $s['runa']],
                        'partidas' => $partidas,
                        'vitorias' => $vitorias,
                        'winrate' => round(($vitorias / max(1, $partidas)) * 100, 2),
                    ]);
                }

                foreach ($statsMatchup as $s) {
                    $partidas = (int) $s['partidas'];
                    $vitorias = (int) $s['vitorias'];
                    ChampionMatchup::create([
                        'campeao' => $s['campeao'],
                        'adversario' => $s['adversario'],
                        'posicao' => $s['posicao'],
                        'patch' => $s['patch'],
                        'partidas' => $partidas,
                        'vitorias' => $vitorias,
                        'winrate' => round(($vitorias / max(1, $partidas)) * 100, 2),
                    ]);
                }
            });

            $this->info('Agregacao concluida com sucesso.');
            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Falha na agregacao: '.$e->getMessage());
            return self::FAILURE;
        }
    }

    private function normalizarPatch(string $versao): string
    {
        $partes = explode('.', $versao);
        if (count($partes) >= 2) {
            return $partes[0].'.'.$partes[1];
        }

        return $versao;
    }

    private function normalizarPosicao(?string $posicao): ?string
    {
        $valor = strtoupper(trim((string) $posicao));
        if ($valor === '' || in_array($valor, ['NONE', 'INVALID'], true)) {
            return null;
        }
        return $valor;
    }

    private function extrairRunaPrimaria(mixed $runas): ?int
    {
        if (!is_array($runas)) {
            return null;
        }

        $styles = $runas['styles'] ?? null;
        if (!is_array($styles) || empty($styles)) {
            return null;
        }

        $primeiro = $styles[0] ?? null;
        if (!is_array($primeiro)) {
            return null;
        }

        $selections = $primeiro['selections'] ?? null;
        if (is_array($selections) && isset($selections[0]['perk'])) {
            return (int) $selections[0]['perk'];
        }

        if (isset($primeiro['style'])) {
            return (int) $primeiro['style'];
        }

        return null;
    }
}
