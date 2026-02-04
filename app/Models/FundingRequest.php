<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'request_number',
        'title',
        'type',
        'amount_requested',
        'amount_approved',
        'duration',
        'description',
        'project_location',
        'expected_jobs',
        'tps_estimated',
        'tps_final',
        'status',
        'evaluation_status',
        'submitted_at',
        'reviewed_at',
        'committee_review_started_at',
        'committee_decision_at',
        'approved_at',
        'funded_at',
        'local_committee_country',
        'is_predefined',
        'funding_type_id',
        'payment_motif',
        'expected_payment',
        'validated_at',
        'validated_by',
        'paid_at',
        'admin_validation_notes',
    ];

    protected $casts = [
        'amount_requested' => 'decimal:2',
        'amount_approved' => 'decimal:2',
        'tps_estimated' => 'decimal:4',
        'tps_final' => 'decimal:4',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'committee_review_started_at' => 'datetime',
        'committee_decision_at' => 'datetime',
        'approved_at' => 'datetime',
        'funded_at' => 'datetime',
        'is_predefined' => 'boolean',
        'validated_at' => 'datetime',
        'paid_at' => 'datetime',
        'expected_payment' => 'decimal:2',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function committeeDecisions()
    {
        return $this->hasMany(CommitteeDecision::class);
    }

    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }

    public function latestCommitteeDecision()
    {
        return $this->hasOne(CommitteeDecision::class)->latest();
    }

    // Accessors
    public function getFormattedAmountRequestedAttribute()
    {
        return number_format($this->amount_requested, 0, ',', ' ') . ' FCFA';
    }

    public function getFormattedAmountApprovedAttribute()
    {
        return $this->amount_approved ? number_format($this->amount_approved, 0, ',', ' ') . ' FCFA' : null;
    }

    public function getTpsEstimatedPercentageAttribute()
    {
        return $this->tps_estimated ? round($this->tps_estimated * 100, 2) . '%' : null;
    }

    public function getTpsFinalPercentageAttribute()
    {
        return $this->tps_final ? round($this->tps_final * 100, 2) . '%' : null;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'draft' => 'Brouillon',
            'submitted' => 'Soumis',
            'under_review' => 'En examen',
            'pending_committee' => 'Comité local',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            'funded' => 'Financé',
            'in_progress' => 'En cours',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'submitted', 'under_review', 'pending_committee']);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeFunded($query)
    {
        return $query->where('status', 'funded');
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('local_committee_country', $country);
    }

    // Méthodes
    public function canBeEdited()
    {
        return in_array($this->status, ['draft']);
    }

    public function canBeSubmitted()
    {
        return $this->status === 'draft';
    }

    public function calculateMonthlyRepayment()
    {
        if (! $this->amount_approved || ! $this->duration) {
            return 0;
        }

        // Capital + TPS
        $tpsAmount = $this->amount_approved * ($this->tps_final ?? 0.05);
        $totalToRepay = $this->amount_approved + $tpsAmount;

        return $totalToRepay / $this->duration;
    }

    public function fundingType()
    {
        return $this->belongsTo(FundingType::class);
    }

    public function payments()
    {
        return $this->hasMany(FundingPayment::class);
    }

    public function requestDocuments()
    {
        return $this->hasMany(FundingDocument::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function scopeNeedsPayment($query)
    {
        return $query->whereIn('status', ['pending_payment', 'validated']);
    }

    public function scopeAwaitingValidation($query)
    {
        return $query->where('status', 'submitted')->where('is_predefined', false);
    }
}
