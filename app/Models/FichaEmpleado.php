<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichaEmpleado extends Model
{
    use HasFactory;

    protected $table = 'ficha_empleado';
    public $timestamps = false;

    protected $appends = [
        'nombreCargo',
        'nombreUnidad',
        'nombreDistrito',
    ];

    // ===== RELACIONES =====

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'id_cargo');
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'id_unidad');
    }

    public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'id_distrito');
    }

    // ===== ACCESSORS =====

    public function getNombreCargoAttribute()
    {
        return $this->cargo->nombre ?? '';
    }

    public function getNombreUnidadAttribute()
    {
        return $this->unidad->nombre ?? '';
    }

    public function getNombreDistritoAttribute()
    {
        return $this->distrito->nombre ?? '';
    }
}
