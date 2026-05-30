<?php

namespace App\Http\Controllers;

use App\Models\ChampionMatchup;
use Illuminate\Http\Request;

class ChampionMatchupController extends Controller
{
    public function index(Request $request)
    {
        $query = ChampionMatchup::query();

        if ($request->filled('patch')) {
            $query->where('patch', (string) $request->query('patch'));
        }

        if ($request->filled('posicao')) {
            $query->where('posicao', strtoupper((string) $request->query('posicao')));
        }

        $minPartidas = max(1, (int) $request->query('min_partidas', 5));
        $query->where('partidas', '>=', $minPartidas);

        return $query
            ->orderByDesc('partidas')
            ->orderByDesc('winrate')
            ->paginate(50);
    }

    public function show(string $campeao, Request $request)
    {
        $query = ChampionMatchup::query()
            ->where('campeao', $campeao);

        if ($request->filled('patch')) {
            $query->where('patch', (string) $request->query('patch'));
        }

        if ($request->filled('posicao')) {
            $query->where('posicao', strtoupper((string) $request->query('posicao')));
        }

        $minPartidas = max(1, (int) $request->query('min_partidas', 5));
        $query->where('partidas', '>=', $minPartidas);

        return $query
            ->orderByDesc('partidas')
            ->orderByDesc('winrate')
            ->get();
    }
}
