<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChampionRuneStat extends Model
{
    protected $table = 'champion_rune_stats';

    protected $fillable = [
        'campeao',
        'posicao',
        'patch',
        'runa',
        'runas',
        'partidas',
        'vitorias',
        'winrate',
        'pickrate',
    ];

    protected $casts = [
        'runas' => 'array',
    ];
}
