<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchParticipante extends Model
{
    protected $table = 'match_participantes';

    protected $fillable = [
        'match_id',
        'puuid',
        'campeao',
        'lane',
        'role',
        'venceu',
        'kills',
        'deaths',
        'assists',
        'gold',
        'farm',
        'dano_total',
        'itens',
        'runas',
        'feiticos',
    ];

    protected $casts = [
        'venceu' => 'boolean',
        'itens' => 'array',
        'runas' => 'array',
        'feiticos' => 'array',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class, 'match_id');
    }
}
