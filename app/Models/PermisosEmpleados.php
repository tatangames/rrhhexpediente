<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisosEmpleados extends Model
{
    use HasFactory;

    protected $table = 'permisos_empleados';
    public $timestamps = false;

    // Relación con Unidad
    public function unidad()
    {
        return $this->belongsTo(PermisosUnidades::class, 'id_unidad');
    }

    // Relación con Cargo
    public function cargo()
    {
        return $this->belongsTo(PermisosCargos::class, 'id_cargo');
    }

}
