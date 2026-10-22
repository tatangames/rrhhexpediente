<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionTitulo extends Model
{
    use HasFactory;

    protected $table = 'evaluacion_titulo';
    public $timestamps = false;


    protected $fillable = [
        'id_evaluacion_tipo',
        'nombre',
        'descripcion',
        'estado',
        'posicion',
    ];

    public function tipo()
    {
        return $this->belongsTo(EvaluacionTipo::class, 'id_evaluacion_tipo');
    }

    public function detalles()
    {
        return $this->hasMany(EvaluacionDetalle::class, 'id_evaluacion_titulo');
    }

}
