<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permisos_consultamedica', function (Blueprint $table) {
            $table->id();
            // Nombre no cambia
            $table->foreignId('id_empleado')->constrained('empleados');

            // Estos datos son copias porque si pueden cambiar
            $table->string('unidad', 100)->nullable();
            $table->string('cargo', 100)->nullable();

            $table->date('fecha');

            // 0: Dia completo
            // 1: Fraccionado
            $table->boolean('condicion');

            // Dias completos
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();

            // Fraccionados
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();

            $table->text('razon')->nullable();

            $table->string('unidad_atencion',800)->nullable();
            $table->string('especialidad', 500)->nullable();
            $table->text('condicion_medica')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisos_consultamedica');
    }
};
