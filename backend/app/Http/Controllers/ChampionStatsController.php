<?php

namespace App\Http\Controllers;

use App\Models\ChampionItemStat;
use App\Models\ChampionMatchup;
use App\Models\ChampionPositionStat;
use App\Models\ChampionRuneStat;
use App\Models\ChampionStat;
use Illuminate\Http\Request;

class ChampionStatsController extends Controller
{
    public function index(Request $request)
    {
        $query = ChampionStat::query();

        if ($request->filled('patch')) {
            $query->where('patch', (string) $request->query('patch'));
        }

        return $query
            ->orderByDesc('partidas')
            ->orderByDesc('winrate')
            ->paginate(50);
    }

    public function show(string $campeao, Request $request)
    {
        $patch = $request->query('patch');

        $baseCampeao = ChampionStat::query()->where('campeao', $campeao);
        $basePosicao = ChampionPositionStat::query()->where('campeao', $campeao);
        $baseItens = ChampionItemStat::query()->where('campeao', $campeao);
        $baseRunas = ChampionRuneStat::query()->where('campeao', $campeao);
        $baseMatchups = ChampionMatchup::query()->where('campeao', $campeao);

        if ($patch) {
            $patch = (string) $patch;
            $baseCampeao->where('patch', $patch);
            $basePosicao->where('patch', $patch);
            $baseItens->where('patch', $patch);
            $baseRunas->where('patch', $patch);
            $baseMatchups->where('patch', $patch);
        }

        return response()->json([
            'geral' => $baseCampeao->orderByDesc('partidas')->get(),
            'posicoes' => $basePosicao->orderByDesc('partidas')->get(),
            'itens' => $baseItens->orderByDesc('partidas')->limit(50)->get(),
            'runas' => $baseRunas->orderByDesc('partidas')->limit(50)->get(),
            'matchups' => $baseMatchups->orderByDesc('partidas')->limit(50)->get(),
        ]);
    }
}
