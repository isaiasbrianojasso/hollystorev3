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
        Schema::create('apis', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('tipo');
            $table->string('descripcion');
            $table->integer('estatus');
            $table->string("url")->nullable();
            $table->string("response_ok")->nullable();
            $table->string("response_fail")->nullable();
            $table->string("method")->nullable();
            $table->string("json")->nullable();
            $table->string("p1")->nullable();
            $table->string("p2")->nullable();
            $table->string("p3")->nullable();
            $table->string("p4")->nullable();
            $table->string("p6")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apis');
    }
};
