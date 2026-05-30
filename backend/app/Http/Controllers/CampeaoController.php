<?php

namespace App\Http\Controllers;

use App\Models\Campeao;

class CampeaoController extends Controller
{
    public function index()
    {
        return Campeao::all();
    }

    public function show(string $riotId)
    {
        return Campeao::where('riot_id', $riotId)->firstOrFail();
    }
}
