<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchModel extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'match_id',
        'riot_match_id',
        'versao',
        'queue_id',
        'duracao',
        'jogada_em',
    ];

    protected $casts = [
        'jogada_em' => 'datetime',
    ];

    public function participantes(): HasMany
    {
        return $this->hasMany(MatchParticipante::class, 'match_id');
    }
}
