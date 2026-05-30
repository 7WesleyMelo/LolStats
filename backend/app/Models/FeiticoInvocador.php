<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeiticoInvocador extends Model
{
    protected $table = 'feiticos_invocador';

    protected $fillable = [
        'riot_id',
        'riot_key',
        'nome',
        'descricao',
        'cooldown',
        'modos',
        'imagem',
        'versao',
    ];

    protected $casts = [
        'modos' => 'array',
    ];

}
