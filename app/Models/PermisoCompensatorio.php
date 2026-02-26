<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisoCompensatorio extends Model
{
    use HasFactory;

    protected $table = 'permisos_compensatorio';
    public $timestamps = false;


    protected $fillable = [
        'id_empleado',
        'unidad',
        'cargo',
        'fecha',
        'condicion',
        'fecha_fraccionado',
        'fecha_inicio',
        'fecha_fin',
        'hora_inicio',
        'hora_fin',
        'razon'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }
}
