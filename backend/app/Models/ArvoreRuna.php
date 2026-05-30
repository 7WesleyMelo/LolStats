<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArvoreRuna extends Model
{
    protected $table = 'arvores_runas';

    protected $fillable = [
        'riot_id',
        'nome',
        'chave',
        'icone',
    ];

    public function runas()
    {
        return $this->hasMany(Runa::class);
    }
}
