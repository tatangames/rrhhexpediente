<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     *
     */
    public function up(): void
    {
        Schema::create('ficha_beneficiario', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_administrador')->unsigned();

            $table->string('nombre', 100)->nullable();
            $table->string('parentesco', 100)->nullable();
            $table->integer('edad')->default(0)->nullable();
            $table->integer('porcentaje')->default(0)->nullable();

            $table->foreign('id_administrador')->references('id')->on('administrador');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ficha_beneficiario');
    }
};
