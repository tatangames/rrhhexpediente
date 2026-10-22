<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// MODULO EVALUACION

class EvaluacionDetalle extends Model
{
    use HasFactory;

    protected $table = 'evaluacion_detalle';
    public $timestamps = false;


    protected $fillable = ['id_evaluacion_titulo', 'nombre', 'puntos', 'posicion'];

}
