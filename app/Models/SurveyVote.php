<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'option_id',
        'user_id',
        'voted_at'
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function option()
    {
        return $this->belongsTo(SurveyOption::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}