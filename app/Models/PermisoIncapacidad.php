<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisoIncapacidad extends Model
{
    use HasFactory;

    protected $table = 'permisos_incapacidad';


    protected $fillable = [
        'id_empleado',
        'unidad',
        'cargo',
        'fecha',
        'id_tipo_incapacidad',
        'id_riesgo',
        'fecha_inicio',
        'dias',
        'fecha_fin',
        'diagnostico',
        'numero',
        'hospitalizacion',
        'fecha_inicio_hospitalizacion',
        'fecha_fin_hospitalizacion',
    ];

    // Relaciones
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    public function tipoIncapacidad()
    {
        return $this->belongsTo(TipoIncapacidad::class, 'tipo_incapacidad_id');
    }

    public function riesgo()
    {
        return $this->belongsTo(Riesgo::class, 'riesgo_id');
    }
}
