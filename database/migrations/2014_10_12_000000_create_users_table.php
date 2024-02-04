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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('rol_id')->nullable();
            $table->integer('plan_id')->nullable();
            $table->date('fechaactivo')->nullable();
            $table->date('fechafinal')->nullable();
            $table->integer('estatus')->nullable();
            $table->string('telefono')->nullable();
            $table->string('chatid')->nullable();
            $table->string("c1")->nullable();
            $table->string("c2")->nullable();
            $table->string("c3")->nullable();
            $table->string("c4")->nullable();
            $table->string("c5")->nullable();
            $table->string("c6")->nullable();
            $table->string("c7")->nullable();
            $table->float('creditos')->nullable();
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
