<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisosCargos extends Model
{
    use HasFactory;

    protected $table = 'permisos_cargos';
    public $timestamps = false;
}
