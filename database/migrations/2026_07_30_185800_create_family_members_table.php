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
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            // Llave foránea que enlaza con la tabla 'censuses'
            $table->foreignId('census_id')->constrained('censuses')->onDelete('cascade');
            
            $table->string('nombre');
            $table->boolean('es_menor')->default(false);
            $table->string('nacionalidad')->nullable();
            $table->string('cedula')->nullable();
            $table->string('parentesco');
            $table->string('sexo');
            $table->date('fecha_nacimiento');
            $table->string('edad');
            $table->string('nivel_educativo');
            $table->string('ocupacion')->nullable();
            $table->string('tiene_discapacidad');
            $table->string('discapacidad')->nullable();
            $table->string('otra_discapacidad')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};