<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// MODULO PERMISOS

class PermisoTipoPermisos extends Model
{
    use HasFactory;

    protected $table = 'permisos_tipopermisos';
    public $timestamps = false;
}
