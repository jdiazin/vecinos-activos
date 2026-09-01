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
        Schema::create('password_requests', function (Blueprint $table) {
            $table->id();
            $table->string('email'); // Correo actual o con el que intenta entrar
            $table->string('cedula')->nullable(); // Identificación del vecino
            $table->text('motivo'); // Qué datos olvidó o necesita cambiar
            $table->string('status')->default('pendiente'); // pendiente, atendido
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_requests');
    }
};