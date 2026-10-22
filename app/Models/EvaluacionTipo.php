<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionTipo extends Model
{
    use HasFactory;

    protected $table = 'evaluacion_tipo';
    public $timestamps = false;

    protected $fillable = ['nombre'];
}
