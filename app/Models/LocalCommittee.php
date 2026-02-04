<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalCommittee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'members',
        'contact_email',
        'contact_phone',
        'address',
        'city',
        'is_active',
        'projects_reviewed',
        'projects_approved',
        'total_amount_approved'
    ];

    protected $casts = [
        'members' => 'array',
        'is_active' => 'boolean',
        'total_amount_approved' => 'decimal:2'
    ];

    // Relations
    public function committeeDecisions()
    {
        return $this->hasMany(CommitteeDecision::class);
    }

    public function fundingRequests()
    {
        return $this->hasManyThrough(FundingRequest::class, CommitteeDecision::class, 'local_committee_id', 'id', 'id', 'funding_request_id');
    }

    // Accessors
    public function getCountryLabelAttribute()
    {
        $countries = [
            'senegal' => 'Sénégal',
            'cote_ivoire' => 'Côte d\'Ivoire',
            'mali' => 'Mali',
            'burkina_faso' => 'Burkina Faso',
            'guinee' => 'Guinée',
            'benin' => 'Bénin',
            'togo' => 'Togo',
            'niger' => 'Niger',
            'mauritanie' => 'Mauritanie'
        ];

        return $countries[$this->country] ?? $this->country;
    }

    public function getFormattedTotalAmountAttribute()
    {
        return number_format($this->total_amount_approved, 0, ',', ' ') . ' FCFA';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Méthodes
    public function incrementProjectsReviewed()
    {
        $this->increment('projects_reviewed');
    }

    public function incrementProjectsApproved()
    {
        $this->increment('projects_approved');
    }

    public function addApprovedAmount($amount)
    {
        $this->increment('total_amount_approved', $amount);
    }
}