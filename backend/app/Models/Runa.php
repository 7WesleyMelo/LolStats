<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Runa extends Model
{
    protected $table = 'runas';

    protected $fillable = [
        'riot_id',
        'arvore_runa_id',
        'slot',
        'nome',
        'descricao',
        'icone',
    ];

    public function arvore()
    {
        return $this->belongsTo(ArvoreRuna::class);
    }
}
