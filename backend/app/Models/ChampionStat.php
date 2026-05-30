<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChampionStat extends Model
{
    protected $table = 'champion_stats';

    protected $fillable = [
        'campeao',
        'patch',
        'partidas',
        'vitorias',
        'winrate',
        'pickrate',
        'kda',
        'kills_medias',
        'deaths_medias',
        'assists_medias',
        'farm_medio',
        'gold_medio',
        'damage_medio',
    ];
}
