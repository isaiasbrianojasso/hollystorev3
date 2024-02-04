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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('tipo');
            $table->string('descripcion');
            $table->integer('estatus');//Activo - No Activo
            $table->string("c1")->nullable();
            $table->string("c2")->nullable();
            $table->string("c3")->nullable();
            $table->string("c4")->nullable();
            $table->string("c5")->nullable();
            $table->string("c6")->nullable();
            $table->string("c7")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
