<?php

namespace App\Http\Controllers;

use App\Models\ChampionPositionStat;
use App\Models\ChampionStat;
use Illuminate\Http\Request;

class ChampionRankingController extends Controller
{
    public function rankings(Request $request)
    {
        $patch = (string) $request->query('patch', '');
        $minPartidas = max(1, (int) $request->query('min_partidas', 20));
        $fatorSuavizacao = max(1, (int) $request->query('fator_suavizacao', 50));

        $query = ChampionStat::query()->where('partidas', '>=', $minPartidas);
        if ($patch !== '') {
            $query->where('patch', $patch);
        }

        $rows = $query->get();
        $totalVitorias = (int) $rows->sum('vitorias');
        $totalPartidas = max(1, (int) $rows->sum('partidas'));
        $winrateGlobal = ($totalVitorias / $totalPartidas) * 100;

        $ranked = $rows->map(function (ChampionStat $row) use ($minPartidas, $fatorSuavizacao, $winrateGlobal) {
            $partidas = (int) $row->partidas;
            $vitorias = (int) $row->vitorias;
            $winrateSuavizada = (($vitorias + (($winrateGlobal / 100) * $fatorSuavizacao)) / ($partidas + $fatorSuavizacao)) * 100;
            $kdaNormalizado = min(6, max(0, (float) $row->kda)) / 6;
            $score = round(
                ($winrateSuavizada * 0.65) +
                ((float) $row->pickrate * 0.20) +
                ($kdaNormalizado * 100 * 0.15),
                2
            );
            $confianca = round(min(100, ($partidas / max(1, $minPartidas * 3)) * 100), 2);

            return [
                'campeao' => $row->campeao,
                'patch' => $row->patch,
                'partidas' => $partidas,
                'vitorias' => $vitorias,
                'winrate' => (float) $row->winrate,
                'winrate_suavizada' => round($winrateSuavizada, 2),
                'pickrate' => (float) $row->pickrate,
                'kda' => (float) $row->kda,
                'confianca' => $confianca,
                'score' => $score,
            ];
        })->sortByDesc('score')->values();

        return response()->json([
            'patch' => $patch !== '' ? $patch : null,
            'min_partidas' => $minPartidas,
            'fator_suavizacao' => $fatorSuavizacao,
            'winrate_global' => round($winrateGlobal, 2),
            'total' => $ranked->count(),
            'dados' => $ranked,
        ]);
    }

    public function tierList(Request $request)
    {
        $patch = (string) $request->query('patch', '');
        $posicao = $request->filled('posicao') ? strtoupper((string) $request->query('posicao')) : null;
        $minPartidas = max(1, (int) $request->query('min_partidas', 20));
        $fatorSuavizacao = max(1, (int) $request->query('fator_suavizacao', 50));

        $query = ChampionPositionStat::query()->where('partidas', '>=', $minPartidas);
        if ($patch !== '') {
            $query->where('patch', $patch);
        }
        if ($posicao) {
            $query->where('posicao', $posicao);
        }

        $rows = $query->get();

        $totalVitorias = (int) $rows->sum('vitorias');
        $totalPartidas = max(1, (int) $rows->sum('partidas'));
        $winrateGlobal = ($totalVitorias / $totalPartidas) * 100;

        $items = $rows->map(function (ChampionPositionStat $row) use ($minPartidas, $fatorSuavizacao, $winrateGlobal) {
            $partidas = (int) $row->partidas;
            $vitorias = (int) $row->vitorias;
            $winrateSuavizada = (($vitorias + (($winrateGlobal / 100) * $fatorSuavizacao)) / ($partidas + $fatorSuavizacao)) * 100;
            $volumeFactor = min(1, $partidas / max(1, $minPartidas * 3));
            $score = round(
                ($winrateSuavizada * 0.75) +
                ($row->pickrate * 0.35 * $volumeFactor),
                2
            );

            return [
                'campeao' => $row->campeao,
                'posicao' => $row->posicao,
                'patch' => $row->patch,
                'partidas' => $partidas,
                'vitorias' => $vitorias,
                'winrate' => (float) $row->winrate,
                'winrate_suavizada' => round($winrateSuavizada, 2),
                'pickrate' => (float) $row->pickrate,
                'confianca' => round(min(100, ($partidas / max(1, $minPartidas * 3)) * 100), 2),
                'score' => $score,
            ];
        })->sortByDesc('score')->values();

        $scores = $items->pluck('score')->values()->all();
        sort($scores);
        $p85 = $this->percentile($scores, 0.85);
        $p65 = $this->percentile($scores, 0.65);
        $p40 = $this->percentile($scores, 0.40);
        $p20 = $this->percentile($scores, 0.20);

        $tiers = [
            'S' => [],
            'A' => [],
            'B' => [],
            'C' => [],
            'D' => [],
        ];

        foreach ($items as $item) {
            $score = $item['score'];
            if ($score >= $p85) {
                $tiers['S'][] = $item;
            } elseif ($score >= $p65) {
                $tiers['A'][] = $item;
            } elseif ($score >= $p40) {
                $tiers['B'][] = $item;
            } elseif ($score >= $p20) {
                $tiers['C'][] = $item;
            } else {
                $tiers['D'][] = $item;
            }
        }

        return response()->json([
            'patch' => $patch !== '' ? $patch : null,
            'posicao' => $posicao,
            'min_partidas' => $minPartidas,
            'fator_suavizacao' => $fatorSuavizacao,
            'winrate_global' => round($winrateGlobal, 2),
            'tiers' => $tiers,
        ]);
    }

    private function percentile(array $sortedValues, float $p): float
    {
        if (empty($sortedValues)) {
            return 0.0;
        }

        $index = (int) floor(($p * (count($sortedValues) - 1)));
        return (float) ($sortedValues[$index] ?? 0);
    }
}
