<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChampionMatchup extends Model
{
    protected $table = 'champion_matchups';

    protected $fillable = [
        'campeao',
        'adversario',
        'posicao',
        'patch',
        'partidas',
        'vitorias',
        'winrate',
    ];
}
