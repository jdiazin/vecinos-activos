<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CensoFamilia extends Model
{
    use HasFactory;

    protected $table = 'censuses';

    protected $fillable = [
        'user_id',
        'sector_calle',
        'numero_vivienda_dir',
        'fecha_censo',
        'jefe_nombre',
        'jefe_nacionalidad',
        'jefe_cedula',
        'jefe_edad',
        'jefe_fecha_nacimiento',
        'jefe_sexo',
        'jefe_estado_civil',
        'jefe_telefono',
        'jefe_telefono_alt',
        'jefe_instruccion',
        'jefe_ocupacion',
        'posee_carnet_patria',
        'codigo_carnet',
        'serial_carnet',
        'tipo_vivienda',
        'condicion_juridica',
        'estado_infraestructura',
        'material_paredes',
        'material_techo',
        'abastecimiento_agua',
        'aguas_servidas',
        'acceso_gas',
        'empresa_gas',
        'conexion_electrica',
        'aseo_urbano',
        'recibe_clap',
        'frecuencia_clap',
        'ingreso_familiar',
        'recibe_remesas',
        'monto_remesas',
        'frecuencia_remesas',
        'dificultad_canasta',
        'motivo_dificultad_canasta',
        'embarazadas_status',
        'embarazadas_cantidad',
        'embarazadas_control',
        'lactantes_status',
        'lactantes_cantidad',
        'adultos_status',
        'adultos_cantidad',
        'encamados_status',
        'encamados_cantidad',
        'enfermedades_cronicas_status',
        'enfermedades_cronicas_detalle',
        'conapdis',
        'observaciones',
    ];

    // Relación con el usuario propietario del censo
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con los integrantes
    public function integrantes()
    {
        return $this->hasMany(FamilyMember::class, 'census_id');
    }
}