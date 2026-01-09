<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';
   // public $timestamps = false;

    protected $fillable = [
        'nombre_original',
        'archivo',
        'ruta',
        'disk',
        'size',
        'id_administrador',
    ];
}
