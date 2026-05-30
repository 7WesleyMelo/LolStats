<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'itens';

    protected $fillable = [
        'riot_id',
        'nome',
        'descricao',
        'descricao_simples',
        'preco_base',
        'preco_total',
        'preco_venda',
        'compravel',
        'tags',
        'mapas',
        'stats',
        'from',
        'into',
        'imagem',
        'versao',
    ];

    protected $casts = [
        'compravel' => 'boolean',
        'tags' => 'array',
        'mapas' => 'array',
        'stats' => 'array',
        'from' => 'array',
        'into' => 'array',
    ];
}
