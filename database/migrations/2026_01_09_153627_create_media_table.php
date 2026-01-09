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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_administrador')->unsigned();

            $table->string('nombre_original');
            $table->string('archivo');
            $table->string('ruta');
            $table->string('disk')->default('expedientes');
            $table->bigInteger('size');

            $table->foreign('id_administrador')->references('id')->on('administrador');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
