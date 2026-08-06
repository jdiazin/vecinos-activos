<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voto extends Model
{
    protected $fillable = ['user_id', 'postulado_id', 'voceria_name'];

    
    public function postulado()
    {
        return $this->belongsTo(Postulacion::class, 'postulado_id');
    }
}