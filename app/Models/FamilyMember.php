<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    use HasFactory;

    protected $table = 'family_members';

    protected $fillable = [
        'census_id', 
        'nombre',
        'es_menor',
        'nacionalidad',
        'cedula',
        'parentesco',
        'sexo',
        'fecha_nacimiento',
        'edad',
        'nivel_educativo',
        'ocupacion',
        'tiene_discapacidad',
        'discapacidad',
        'otra_discapacidad',
    ];

    // Relación con el censo principal
    public function censo()
    {
        return $this->belongsTo(CensoFamilia::class, 'census_id');
    }
}