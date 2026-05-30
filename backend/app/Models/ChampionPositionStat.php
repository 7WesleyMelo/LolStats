<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChampionPositionStat extends Model
{
    protected $table = 'champion_position_stats';

    protected $fillable = [
        'campeao',
        'posicao',
        'patch',
        'partidas',
        'vitorias',
        'winrate',
        'pickrate',
    ];
}
