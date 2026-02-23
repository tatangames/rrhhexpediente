<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    use HasFactory;

    protected $table = 'evaluacion';
    public $timestamps = false;


    protected $fillable = ['nombre', 'descripcion', 'estado', 'posicion'];

    public function detalles()
    {
        return $this->hasMany(EvaluacionDetalle::class, 'evaluacion_id')
            ->where('estado', true)
            ->orderBy('posicion');
    }
}
