<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id',
        'title',
        'description',
        'questions',
        'passing_score',
        'time_limit_minutes',
        'max_attempts',
        'is_active'
    ];

    protected $casts = [
        'questions' => 'array',
        'is_active' => 'boolean',
        'passing_score' => 'integer',
        'time_limit_minutes' => 'integer',
        'max_attempts' => 'integer'
    ];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}