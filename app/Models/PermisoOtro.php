<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisoOtro extends Model
{
    use HasFactory;

    protected $table = 'permisos_otros';
    public $timestamps = false;


    protected $fillable = [
        'id_empleado',
        'unidad',
        'cargo',
        'fecha',
        'fecha_fraccionado',
        'condicion',
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
