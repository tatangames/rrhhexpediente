<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// MODULO PERMISOS

class PermisoRiesgo extends Model
{
    use HasFactory;

    protected $table = 'permisos_riesgos';
    public $timestamps = false;
}
