<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformacionGeneral extends Model
{
    protected $table = 'informacion_general';
    public $timestamps = false;

    protected $fillable = [
        'px_firmas',
        'salto_pagina',
        'jefe',
        'cargo',
        'area',
    ];

    protected $casts = [
        'salto_pagina' => 'boolean',
    ];
}
