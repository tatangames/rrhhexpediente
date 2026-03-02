<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// MODULO EVALUACION

class EvaluacionUnidad extends Model
{
    use HasFactory;

    protected $table = 'evaluacion_unidad';
    public $timestamps = false;
}
