<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichaEmpleado extends Model
{
    use HasFactory;

    protected $table = 'ficha_empleado';
    public $timestamps = false;
}
