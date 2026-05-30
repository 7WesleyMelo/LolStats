<?php

namespace App\Console\Commands;

use App\Models\MatchModel;
use App\Models\MatchParticipante;
use App\Services\Riot\MatchV5Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class SincronizarPartidasCommand extends Command
{
    protected $signature = 'lol:sincronizar-partidas 
                            {puuid : PUUID do jogador}
                            {--inicio=0 : Offset inicial}
                            {--quantidade=20 : Quantidade de partidas (max 100)}';

    protected $description = 'Sincroniza partidas recentes de um PUUID via Match-V5.';

    public function handle(MatchV5Service $matchV5): int
    {
        $puuid = (string) $this->argument('puuid');
        $inicio = (int) $this->option('inicio');
        $quantidade = min(100, max(1, (int) $this->option('quantidade')));

        $this->info("Buscando partidas de {$puuid}...");

        try {
            $matchIds = $matchV5->matchIdsByPuuid($puuid, $inicio, $quantidade);

            if (empty($matchIds)) {
                $this->warn('Nenhuma partida encontrada para os parametros informados.');
                return self::SUCCESS;
            }

            $salvas = 0;

            foreach ($matchIds as $matchId) {
                $dadosMatch = $matchV5->matchById($matchId);
                $info = $dadosMatch['info'] ?? [];
                $metadata = $dadosMatch['metadata'] ?? [];

                DB::transaction(function () use ($info, $metadata, $matchId, &$salvas): void {
                    $match = MatchModel::updateOrCreate(
                        ['riot_match_id' => $matchId],
                        [
                            'match_id' => $matchId,
                            'versao' => (string) ($info['gameVersion'] ?? ''),
                            'queue_id' => (string) ($info['queueId'] ?? ''),
                            'duracao' => (int) ($info['gameDuration'] ?? 0),
                            'jogada_em' => now()->setTimestamp((int) floor(($info['gameEndTimestamp'] ?? 0) / 1000)),
                        ]
                    );

                    MatchParticipante::query()->where('match_id', $match->id)->delete();

                    foreach (($info['participants'] ?? []) as $p) {
                        MatchParticipante::create([
                            'match_id' => $match->id,
                            'puuid' => (string) ($p['puuid'] ?? ''),
                            'campeao' => (string) ($p['championName'] ?? ''),
                            'lane' => $p['lane'] ?? $p['teamPosition'] ?? null,
                            'role' => $p['role'] ?? null,
                            'venceu' => (bool) ($p['win'] ?? false),
                            'kills' => (int) ($p['kills'] ?? 0),
                            'deaths' => (int) ($p['deaths'] ?? 0),
                            'assists' => (int) ($p['assists'] ?? 0),
                            'gold' => (int) ($p['goldEarned'] ?? 0),
                            'farm' => (int) (($p['totalMinionsKilled'] ?? 0) + ($p['neutralMinionsKilled'] ?? 0)),
                            'dano_total' => (int) ($p['totalDamageDealtToChampions'] ?? 0),
                            'itens' => [
                                $p['item0'] ?? 0,
                                $p['item1'] ?? 0,
                                $p['item2'] ?? 0,
                                $p['item3'] ?? 0,
                                $p['item4'] ?? 0,
                                $p['item5'] ?? 0,
                                $p['item6'] ?? 0,
                            ],
                            'runas' => $p['perks'] ?? [],
                            'feiticos' => [
                                $p['summoner1Id'] ?? null,
                                $p['summoner2Id'] ?? null,
                            ],
                        ]);
                    }

                    $salvas++;
                });
            }

            $this->info("Sincronizacao concluida. Partidas processadas: {$salvas}.");
            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Falha ao sincronizar partidas: '.$e->getMessage());
            return self::FAILURE;
        }
    }
}
