<?php

namespace App\Http\Controllers;

use App\Models\MatchModel;

class MatchController extends Controller
{
    public function index()
    {
        return MatchModel::query()
            ->withCount('participantes')
            ->orderByDesc('jogada_em')
            ->paginate(20);
    }

    public function show(string $riotMatchId)
    {
        return MatchModel::query()
            ->with('participantes')
            ->where('riot_match_id', $riotMatchId)
            ->firstOrFail();
    }
}
