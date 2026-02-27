<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisoIncapacidad extends Model
{
    use HasFactory;

    protected $table = 'permisos_incapacidad';
    // timestamps = true (tiene created_at / updated_at en el schema)

    protected $fillable = [
        'id_empleado',
        'unidad',
        'cargo',
        'fecha',
        'id_tipo_incapacidad',   // FK real en la tabla
        'id_riesgo',             // FK real en la tabla
        'fecha_inicio',
        'dias',
        'fecha_fin',
        'diagnostico',
        'numero',
        'hospitalizacion',
        'fecha_inicio_hospitalizacion',
        'fecha_fin_hospitalizacion',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    // 'id_tipo_incapacidad' → tabla 'tipo_incapacidad'
    public function tipoIncapacidad()
    {
        return $this->belongsTo(TipoIncapacidad::class, 'id_tipo_incapacidad');
    }

    // 'id_riesgo' → tabla 'riesgos'
    public function riesgo()
    {
        return $this->belongsTo(Riesgo::class, 'id_riesgo');
    }
}
