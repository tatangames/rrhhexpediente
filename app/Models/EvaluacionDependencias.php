<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionDependencias extends Model
{
    use HasFactory;

    protected $table = 'evaluacion_dependencias';
    public $timestamps = false;
}
