<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('postulacions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nombre');
            $table->string('voceria'); // Usaremos 'voceria' en minúsculas por convención
            $table->text('propuesta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postulacions');
    }
};