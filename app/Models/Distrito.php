<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// MODULO FICHA

class Distrito extends Model
{
    use HasFactory;

    protected $table = 'distrito';
    public $timestamps = false;
}
