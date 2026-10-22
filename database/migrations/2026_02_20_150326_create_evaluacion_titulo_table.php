<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TITULOS DE LA AVALUACION AFILIADOS A UN TIPO
     */
    public function up(): void
    {
        Schema::create('evaluacion_titulo', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_evaluacion_tipo')->constrained('evaluacion_tipo');

            $table->text('nombre');
            $table->text('descripcion')->nullable();

            $table->boolean('estado')->default(true);

            $table->integer('posicion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluacion_titulo');
    }
};
