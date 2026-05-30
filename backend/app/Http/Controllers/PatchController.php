<?php

namespace App\Http\Controllers;

use App\Models\Patch;

class PatchController extends Controller
{
    public function index()
    {
        return Patch::orderByDesc('versao')->get();
    }
}
