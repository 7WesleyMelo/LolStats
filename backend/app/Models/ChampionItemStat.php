<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChampionItemStat extends Model
{
    protected $table = 'champion_item_stats';

    protected $fillable = [
        'campeao',
        'posicao',
        'patch',
        'item',
        'itens',
        'partidas',
        'vitorias',
        'winrate',
    ];

    protected $casts = [
        'itens' => 'array',
    ];
}
