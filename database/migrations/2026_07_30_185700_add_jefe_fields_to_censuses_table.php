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
        Schema::create('censuses', function (Blueprint $table) {
            $table->id();
            
            // Campos de ubicación y fecha del censo
            $table->string('sector_calle')->nullable();
            $table->string('numero_vivienda_dir')->nullable();
            $table->date('fecha_censo')->nullable();

            // Datos del jefe de familia
            $table->string('jefe_nombre')->nullable();
            $table->string('jefe_nacionalidad', 1)->nullable();
            $table->string('jefe_cedula')->nullable();
            $table->integer('jefe_edad')->nullable();
            $table->date('jefe_fecha_nacimiento')->nullable();
            $table->string('jefe_sexo')->nullable();
            $table->string('jefe_estado_civil')->nullable();
            $table->string('jefe_telefono')->nullable();
            $table->string('jefe_telefono_alt')->nullable();
            $table->string('jefe_instruccion')->nullable();
            $table->string('jefe_ocupacion')->nullable();
            $table->string('posee_carnet_patria')->nullable();
            $table->string('codigo_carnet')->nullable();
            $table->string('serial_carnet')->nullable();

            // Vivienda y servicios
            $table->string('tipo_vivienda')->nullable();
            $table->string('condicion_juridica')->nullable();
            $table->string('estado_infraestructura')->nullable();
            $table->string('material_paredes')->nullable();
            $table->string('material_techo')->nullable();
            $table->string('abastecimiento_agua')->nullable();
            $table->string('aguas_servidas')->nullable();
            $table->string('acceso_gas')->nullable();
            $table->string('empresa_gas')->nullable();
            $table->string('conexion_electrica')->nullable();
            $table->string('aseo_urbano')->nullable();

            // Socioeconómico
            $table->string('recibe_clap')->nullable();
            $table->string('frecuencia_clap')->nullable();
            $table->string('ingreso_familiar')->nullable();
            $table->string('recibe_remesas')->nullable();
            $table->decimal('monto_remesas', 10, 2)->nullable();
            $table->string('frecuencia_remesas')->nullable();
            $table->string('dificultad_canasta')->nullable();
            $table->text('motivo_dificultad_canasta')->nullable();

            // Salud y vulnerabilidad
            $table->string('embarazadas_status')->nullable();
            $table->integer('embarazadas_cantidad')->nullable();
            $table->string('embarazadas_control')->nullable();
            $table->string('lactantes_status')->nullable();
            $table->integer('lactantes_cantidad')->nullable();
            $table->string('adultos_status')->nullable();
            $table->integer('adultos_cantidad')->nullable();
            $table->string('encamados_status')->nullable();
            $table->integer('encamados_cantidad')->nullable();
            $table->string('enfermedades_cronicas_status')->nullable();
            $table->text('enfermedades_cronicas_detalle')->nullable();
            $table->string('conapdis')->nullable();

            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('censuses');
    }
};