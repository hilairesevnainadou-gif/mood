<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundingCommitteeDecision extends Model
{
    use HasFactory;

    protected $table = 'funding_committee_decisions';

    protected $fillable = [
        'funding_request_id',
        'committee_type',
        'committee_name',
        'committee_code',
        'decision',
        'decision_date',
        'approved_amount',
        'conditions',
        'comments',
        'rejection_reasons',
        'committee_members',
        'total_members',
        'present_members',
        'votes_for',
        'votes_against',
        'votes_abstention',
        'funding_duration',
        'tps_recommended',
        'next_steps',
        'next_review_date',
        'meeting_minutes_path',
        'decision_file_path',
        'metadata',
        'decision_number'
    ];

    protected $casts = [
        'decision_date' => 'date',
        'approved_amount' => 'decimal:2',
        'committee_members' => 'array',
        'tps_recommended' => 'decimal:2',
        'next_review_date' => 'date',
        'metadata' => 'array'
    ];

    // Relations
    public function funding()
    {
        return $this->belongsTo(Funding::class, 'funding_request_id');
    }

    // Accessors
    public function getCommitteeTypeLabelAttribute()
    {
        $labels = [
            'local' => 'Comité Local',
            'regional' => 'Comité Régional',
            'national' => 'Comité National',
            'technical' => 'Comité Technique',
            'financial' => 'Comité Financier',
            'strategic' => 'Comité Stratégique'
        ];

        return $labels[$this->committee_type] ?? $this->committee_type;
    }

    public function getDecisionLabelAttribute()
    {
        $labels = [
            'approved' => 'Approuvé',
            'approved_with_conditions' => 'Approuvé avec conditions',
            'rejected' => 'Rejeté',
            'deferred' => 'Reporté',
            'requires_more_info' => 'Nécessite plus d\'informations',
            'pending' => 'En attente'
        ];

        return $labels[$this->decision] ?? $this->decision;
    }

    public function getApprovalRateAttribute()
    {
        if ($this->present_members > 0) {
            return round(($this->votes_for / $this->present_members) * 100, 2);
        }
        return 0;
    }

    public function getIsApprovedAttribute()
    {
        return in_array($this->decision, ['approved', 'approved_with_conditions']);
    }

    public function getIsRejectedAttribute()
    {
        return $this->decision === 'rejected';
    }

    public function getFormattedApprovedAmountAttribute()
    {
        return $this->approved_amount ? number_format($this->approved_amount, 0, ',', ' ') . ' FCFA' : 'Non défini';
    }

    // Méthodes
    public function getVotingSummary()
    {
        return [
            'for' => $this->votes_for,
            'against' => $this->votes_against,
            'abstention' => $this->votes_abstention,
            'total_present' => $this->present_members,
            'approval_rate' => $this->approval_rate
        ];
    }

    // Génération du numéro de décision
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($decision) {
            if (!$decision->decision_number) {
                $decision->decision_number = static::generateDecisionNumber();
            }
        });
    }

    public static function generateDecisionNumber()
    {
        $prefix = 'BHDM-DEC-';
        $datePart = date('Ymd');
        $lastDecision = static::where('decision_number', 'like', $prefix . $datePart . '-%')
            ->orderBy('decision_number', 'desc')
            ->first();

        if ($lastDecision) {
            $lastNumber = intval(substr($lastDecision->decision_number, -4));
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $datePart . '-' . $nextNumber;
    }
}