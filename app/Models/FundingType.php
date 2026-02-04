<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundingType extends Model {
    protected $fillable =
     [
        'name',
        'code',
        'description',
        'amount',
        'registration_fee',
        'duration_months',
        'required_documents',
        'category',
        'is_active'
        ];

    protected $casts = [
        'required_documents' => 'array',
        'amount' => 'decimal:2',
        'registration_fee' => 'decimal:2',
    ];
}
