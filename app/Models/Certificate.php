<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_id',
        'user_id',
        'training_id',
        'full_name',
        'certificate_number',
        'issue_date',
        'expiry_date',
        'final_score',
        'certificate_url',
        'template',
        'is_verified',
        'verification_code'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'final_score' => 'decimal:2',
        'is_verified' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}