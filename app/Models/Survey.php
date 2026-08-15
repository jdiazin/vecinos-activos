<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
        'created_by'
    ];

    public function options()
    {
        return $this->hasMany(SurveyOption::class);
    }

    public function votes()
    {
        return $this->hasMany(SurveyVote::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}