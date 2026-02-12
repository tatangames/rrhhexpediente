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
        Schema::create('permisos_incapacidad', function (Blueprint $table) {
            $table->id();
            // Relación con empleado
            $table->foreignId('id_empleado')->constrained('empleados');

            // Datos copiados del empleado (pueden cambiar)
            $table->string('unidad', 100)->nullable();
            $table->string('cargo', 100)->nullable();

            // Fecha del documento/registro
            $table->date('fecha');

            // Tipo y riesgo
            $table->foreignId('id_tipo_incapacidad')->constrained('tipo_incapacidad');
            $table->foreignId('id_riesgo')->constrained('riesgos');

            // Período de incapacidad
            $table->date('fecha_inicio');
            $table->integer('dias');
            $table->date('fecha_fin');

            // Detalles
            $table->string('diagnostico', 800)->nullable();
            $table->string('numero', 100)->nullable();

            // Hospitalización
            $table->boolean('hospitalizacion')->default(false);
            $table->date('fecha_inicio_hospitalizacion')->nullable();
            $table->date('fecha_fin_hospitalizacion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisos_incapacidad');
    }
};
