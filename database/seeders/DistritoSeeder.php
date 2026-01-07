<?php

namespace Database\Seeders;

use App\Models\Distrito;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistritoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Distrito::create([
            'nombre' => 'DISTRITO DE SANTA ANA NORTE',
        ]);
        Distrito::create([
            'nombre' => 'DISTRITO SANTA ROSA GUACHIPILIN',
        ]);
        Distrito::create([
            'nombre' => 'DISTRITO MASAHUAT',
        ]);
        Distrito::create([
            'nombre' => 'DISTRITO TEXISTEPEQUE',
        ]);
    }
}
