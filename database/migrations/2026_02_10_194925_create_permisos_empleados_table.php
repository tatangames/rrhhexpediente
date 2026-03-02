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
        Schema::create('permisos_empleados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);

            $table->foreignId('id_unidad')->constrained('permisos_unidades');
            $table->foreignId('id_cargo')->constrained('permisos_cargos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisos_empleados');
    }
};
