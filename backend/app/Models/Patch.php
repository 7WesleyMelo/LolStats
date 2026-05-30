<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patch extends Model
{
    protected $table = 'patches';

    protected $fillable = [
        'versao',
        'ativa',
    ];

    protected $casts = [
        'ativa' => 'boolean',
    ];
}
