<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->string('issue_type');
            $table->string('location');
            $table->text('description');
            $table->string('status')->default('Reportado'); // 'Reportado' o 'Pendiente'
            
            // --- Nuevos campos para la evidencia y control de resolución ---
            $table->string('evidence_path')->nullable(); // Ruta de la foto o documento de prueba
            $table->text('solution_notes')->nullable();  // Descripción de cómo se solucionó
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete(); // Quién lo resolvió
            $table->timestamp('resolved_at')->nullable(); // Fecha y hora exacta de la resolución

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};