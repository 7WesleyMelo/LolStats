<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Summoner extends Model
{
    protected $table = 'summoners';

    protected $fillable = [
        'puuid',
        'summoner_id',
        'nome',
        'tag_line',
        'tier',
        'rank',
        'league_points',
        'wins',
        'losses',
        'region',
        'last_seeded_at',
        'last_matches_fetched_at',
    ];

    protected $casts = [
        'last_seeded_at' => 'datetime',
        'last_matches_fetched_at' => 'datetime',
    ];
}
