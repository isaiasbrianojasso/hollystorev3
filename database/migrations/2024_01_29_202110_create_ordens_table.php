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
        Schema::create('ordens', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('tipo');
            $table->integer('id_api')->unsigned();
            $table->foreign('id_api')->references('id')->on('apis');
        //    $table->string('id_api');
            $table->string('precio');
            $table->integer('estatus');
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
        Schema::dropIfExists('ordens');
    }
};
