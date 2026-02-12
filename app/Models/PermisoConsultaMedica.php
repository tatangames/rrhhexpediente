<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisoConsultaMedica extends Model
{
    use HasFactory;

    protected $table = 'permisos_consultamedica';
    public $timestamps = false;

    protected $fillable = [
        'id_empleado',
        'unidad',
        'cargo',
        'fecha',
        'condicion',
        'fecha_inicio',
        'fecha_fin',
        'hora_inicio',
        'hora_fin',
        'razon',
        'unidad_atencion',
        'especialidad',
        'condicion_medica'
    ];
}
