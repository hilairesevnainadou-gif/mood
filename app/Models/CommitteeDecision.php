<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteeDecision extends Model
{
    use HasFactory;

    protected $fillable = [
        'funding_request_id',
        'local_committee_id',
        'decision',
        'amount_approved',
        'tps_final',
        'comments',
        'conditions',
        'modification_requests',
        'votes_for',
        'votes_against',
        'votes_abstention',
        'review_started_at',
        'decision_date'
    ];

    protected $casts = [
        'amount_approved' => 'decimal:2',
        'tps_final' => 'decimal:4',
        'conditions' => 'array',
        'review_started_at' => 'datetime',
        'decision_date' => 'datetime'
    ];

    // Relations
    public function fundingRequest()
    {
        return $this->belongsTo(FundingRequest::class);
    }

    public function localCommittee()
    {
        return $this->belongsTo(LocalCommittee::class);
    }

    // Accessors
    public function getDecisionLabelAttribute()
    {
        $labels = [
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            'pending_modification' => 'Modifications demandées'
        ];

        return $labels[$this->decision] ?? $this->decision;
    }

    public function getTpsFinalPercentageAttribute()
    {
        return $this->tps_final ? round($this->tps_final * 100, 2) . '%' : null;
    }

    // Méthodes
    public function isApproved()
    {
        return $this->decision === 'approved';
    }

    public function isRejected()
    {
        return $this->decision === 'rejected';
    }

    public function needsModification()
    {
        return $this->decision === 'pending_modification';
    }

    public function getVoteResult()
    {
        $total = $this->votes_for + $this->votes_against + $this->votes_abstention;
        if ($total === 0) return 'Aucun vote';

        $percentage = ($this->votes_for / $total) * 100;
        
        if ($percentage >= 66) return 'Approuvé à l\'unanimité';
        if ($percentage >= 51) return 'Approuvé à la majorité';
        if ($this->votes_against > $this->votes_for) return 'Rejeté';
        
        return 'Vote partagé';
    }
}