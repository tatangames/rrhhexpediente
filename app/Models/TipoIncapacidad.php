<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoIncapacidad extends Model
{
    use HasFactory;

    protected $table = 'tipo_incapacidad';
    public $timestamps = false;
}
