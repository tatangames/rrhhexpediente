<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * FICHA DEL EMPLEADO
     */
    public function up(): void
    {
        Schema::create('ficha_empleado', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_administrador')->unsigned()->nullable();
            $table->bigInteger('id_distrito')->unsigned()->nullable();
            $table->bigInteger('id_cargo')->unsigned()->nullable();
            $table->bigInteger('id_unidad')->unsigned()->nullable();
            $table->bigInteger('id_nivelacademico')->unsigned()->nullable();

            $table->string('nombre', 100)->nullable();
            $table->string('dui', 50)->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->decimal('salario_actual', 10, 2)->nullable();

            // INFORMACION PARTICULAR
            $table->date('fecha_nacimiento')->nullable();
            $table->string('lugar_nacimiento', 100)->nullable();
            $table->string('otro_nivelacademico', 100)->nullable();
            $table->string('profesion', 100)->nullable();
            $table->string('direccion', 100)->nullable();

            // 1: soltero
            // 2: casdo
            // 3: divorciado
            // 4: viudo
            // 5: Acompañado

            $table->integer('estado_civil')->default(1)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('caso_emergencia', 50)->nullable();
            $table->string('celular_emergencia', 20)->nullable();

            $table->string('tipo_padecimiento', 100)->nullable();

            $table->foreign('id_administrador')->references('id')->on('administrador');
            $table->foreign('id_distrito')->references('id')->on('distrito');
            $table->foreign('id_cargo')->references('id')->on('cargo');
            $table->foreign('id_unidad')->references('id')->on('unidad');
            $table->foreign('id_nivelacademico')->references('id')->on('nivel_academico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ficha_empleado');
    }
};
