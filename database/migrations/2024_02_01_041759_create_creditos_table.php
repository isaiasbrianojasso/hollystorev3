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
        Schema::create('creditos', function (Blueprint $table) {
            $table->id();
          //  $table->integer('ID_Usuario')->nullable();

            $table->integer('ID_Usuario')->unsigned();
            $table->foreign('ID_Usuario')->references('id')->on('users');
            $table->float('Cantidad')->nullable();
            $table->boolean('Operacion')->nullable();
          //  $table->integer('ID_Autorizo')->nullable();
            $table->integer('ID_Metodo')->nullable();
            $table->integer('ID_Autorizo')->unsigned();
            $table->foreign('ID_Autorizo')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creditos');
    }
};
