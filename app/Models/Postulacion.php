<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Voto;

class Postulacion extends Model
{
    protected $fillable = ['user_id', 'nombre', 'voceria', 'propuesta'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con los votos emitidos para esta postulación
    public function votos()
    {
        return $this->hasMany(Voto::class, 'postulado_id');
    }
}