<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campeao extends Model
{
    protected $table = 'campeoes';

    protected $fillable = [
        'riot_id',
        'riot_key',
        'nome',
        'titulo',
        'descricao',
        'imagem',
        'tags',
        'info',
        'stats',
        'tipo_recurso',
        'versao',
    ];

    protected $casts = [
        'tags' => 'array',
        'info' => 'array',
        'stats' => 'array',
    ];
}
