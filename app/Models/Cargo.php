<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// MODULO FICHA
class Cargo extends Model
{
    use HasFactory;

    protected $table = 'cargo';
    public $timestamps = false;
}
