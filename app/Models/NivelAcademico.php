<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelAcademico extends Model
{
    use HasFactory;

    protected $table = 'nivel_academico';
    public $timestamps = false;
}
