<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'order',
        'is_active',
        'trainings_count'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'trainings_count' => 'integer'
    ];

    public function trainings()
    {
        return $this->hasMany(Training::class, 'category', 'slug');
    }
}