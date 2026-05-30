<?php

namespace App\Http\Controllers;

use App\Models\Item;

class ItemController extends Controller
{
    public function index()
    {
        return Item::orderBy('nome')->get();
    }

    public function show(string $riotId)
    {
        return Item::where('riot_id', $riotId)->firstOrFail();
    }
}
