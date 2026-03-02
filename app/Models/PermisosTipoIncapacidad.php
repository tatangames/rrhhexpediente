<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisosTipoIncapacidad extends Model
{
    use HasFactory;

    protected $table = 'permisos_tipoincapacidad';
    public $timestamps = false;
}
