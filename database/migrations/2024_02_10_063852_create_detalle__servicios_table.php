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
        Schema::create('detalle__servicios', function (Blueprint $table) {
            $table->id();
            $table->integer('id_servicio')->unsigned();
            $table->foreign('id_servicio')->references('id')->on('servicios');
            $table->integer('id_api')->unsigned();
            $table->foreign('id_api')->references('id')->on('apis');
            $table->string('codigo')->nullable();
            $table->string('imagen')->nullable();
            $table->string('url')->nullable();
            $table->string('descarga')->nullable();
            $table->string('imei')->nullable();
            $table->string('telefono')->nullable();
            $table->string('ip')->nullable();
            $table->string('detalle')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle__servicios');
    }
};
