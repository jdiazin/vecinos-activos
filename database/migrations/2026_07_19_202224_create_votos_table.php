<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('postulado_id')->constrained('postulacions')->onDelete('cascade');
            $table->string('voceria_name');
            $table->timestamps();
            $table->unique(['user_id', 'voceria_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votos');
    }
};