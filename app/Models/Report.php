<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'issue_type',
        'location',
        'description',
        'status',
        'evidence_path',  
        'solution_notes', 
        'resolved_by',    
        'resolved_at'     
    ];

    // Relación: Un reporte pertenece a un usuario (el que lo creó)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: El usuario (Admin/Vocero) que dio solución al reporte
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}