<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionDetalle extends Model
{
    use HasFactory;

    protected $table = 'evaluacion_detalle';
    public $timestamps = false;


    protected $fillable = ['evaluacion_id', 'nombre', 'puntos', 'posicion'];

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id');
    }
}
