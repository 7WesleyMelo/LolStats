<?php

namespace App\Http\Controllers;

use App\Models\FeiticoInvocador;

class FeiticoInvocadorController extends Controller
{
    public function index()
    {
        return FeiticoInvocador::orderBy('nome')->get();
    }
}
