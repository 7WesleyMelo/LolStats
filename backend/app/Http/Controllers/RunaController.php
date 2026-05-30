<?php

namespace App\Http\Controllers;

use App\Models\ArvoreRuna;
use App\Models\Runa;

class RunaController extends Controller
{
    public function index()
    {
        return ArvoreRuna::with('runas')
            ->orderBy('nome')
            ->get();
    }

    public function show(string $riotId)
    {
        return Runa::with('arvore')
            ->where('riot_id', $riotId)
            ->firstOrFail();
    }
}
