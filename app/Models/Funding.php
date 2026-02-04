<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Funding extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'funding_requests';

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
        'credited_at',
        'local_committee_country'
    ];

    protected $casts = [
        'amount_requested' => 'decimal:2',
        'amount_approved' => 'decimal:2',
        'tps_estimated' => 'decimal:2',
        'tps_final' => 'decimal:2',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'committee_review_started_at' => 'datetime',
        'committee_decision_at' => 'datetime',
        'approved_at' => 'datetime',
        'funded_at' => 'datetime',
        'credited_at' => 'datetime',
        'expected_jobs' => 'integer',
        'duration' => 'integer'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function committeeDecision()
    {
        return $this->hasOne(FundingCommitteeDecision::class, 'funding_request_id');
    }

    public function missions()
    {
        return $this->hasMany(FundingMission::class, 'funding_request_id');
    }
    

    public function documents()
    {
        return $this->hasMany(FundingDocument::class, 'funding_request_id');
    }

    public function payments()
    {
        return $this->hasMany(FundingPayment::class, 'funding_request_id');
    }

    public function evaluations()
    {
        return $this->hasMany(FundingEvaluation::class, 'funding_request_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', ['approved', 'funded', 'pending_committee']);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeFunded($query)
    {
        return $query->where('status', 'funded');
    }

    public function scopeCredited($query)
    {
        return $query->where('status', 'credited');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getTypeLabelAttribute()
    {
        $labels = [
            'agriculture' => 'Agriculture',
            'elevage' => 'Élevage',
            'peche' => 'Pêche',
            'transformation' => 'Transformation',
            'artisanat' => 'Artisanat',
            'industrie' => 'Industrie',
            'commerce' => 'Commerce',
            'services' => 'Services',
            'tourisme' => 'Tourisme',
            'transport' => 'Transport',
            'technologie' => 'Technologie',
            'energie_renouvelable' => 'Énergie renouvelable',
            'economie_circulaire' => 'Économie circulaire',
            'autre' => 'Autre'
        ];

        return $labels[$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'draft' => 'Brouillon',
            'submitted' => 'Soumis',
            'under_review' => 'En révision',
            'pending_committee' => 'En attente du comité',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            'funded' => 'Financé',
            'in_progress' => 'En cours',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
            'credited' => 'Accrédité'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getEvaluationStatusLabelAttribute()
    {
        $labels = [
            'mission_1_pending' => 'Mission 1 en attente',
            'mission_1_completed' => 'Mission 1 complétée',
            'mission_2_pending' => 'Mission 2 en attente',
            'mission_2_completed' => 'Mission 2 complétée',
            'under_local_committee_review' => 'En révision par le comité local',
            'committee_decision_pending' => 'Décision du comité en attente'
        ];

        return $labels[$this->evaluation_status] ?? $this->evaluation_status;
    }

    public function getProgressPercentageAttribute()
    {
        switch ($this->evaluation_status) {
            case 'mission_1_completed':
                return 25;
            case 'mission_2_completed':
                return 50;
            case 'under_local_committee_review':
                return 75;
            case 'committee_decision_pending':
                return 90;
            default:
                return 10;
        }
    }

    public function getFormattedAmountRequestedAttribute()
    {
        return number_format($this->amount_requested, 0, ',', ' ') . ' FCFA';
    }

    public function getFormattedAmountApprovedAttribute()
    {
        return $this->amount_approved ? number_format($this->amount_approved, 0, ',', ' ') . ' FCFA' : 'En attente';
    }

    public function getIsPendingAttribute()
    {
        return in_array($this->status, ['approved', 'funded', 'pending_committee']);
    }

    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    public function getIsFundedAttribute()
    {
        return $this->status === 'funded';
    }

    public function getIsCreditedAttribute()
    {
        return $this->status === 'credited';
    }

    public function getCanBeCreditedAttribute()
    {
        return $this->status === 'funded' && !$this->credited_at;
    }

    public function getPaymentScheduleAttribute()
    {
        // Générer un échéancier fictif basé sur la durée
        if (!$this->amount_approved || $this->amount_approved <= 0) {
            return null;
        }

        $schedule = [];
        $totalMonths = $this->duration;
        $monthlyAmount = $this->amount_approved / $totalMonths;
        $currentDate = $this->funded_at ?: now();

        for ($i = 1; $i <= $totalMonths; $i++) {
            $dueDate = $currentDate->copy()->addMonths($i);
            $completed = $dueDate->isPast();
            
            $schedule[] = [
                'title' => 'Tranche ' . $i,
                'due_date' => $dueDate->format('Y-m-d'),
                'amount' => $monthlyAmount,
                'percentage' => round(100 / $totalMonths, 2),
                'completed' => $completed
            ];
        }

        return $schedule;
    }

    public function getRemainingAmountAttribute()
    {
        $totalPaid = $this->payments()->sum('amount');
        return max(0, ($this->amount_approved ?? 0) - $totalPaid);
    }

    // Méthodes d'action
    public function markAsCredited()
    {
        $this->update([
            'status' => 'credited',
            'credited_at' => now()
        ]);
    }

    public function submit()
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now()
        ]);
    }

    public function approve($amount = null)
    {
        $updates = [
            'status' => 'approved',
            'approved_at' => now()
        ];

        if ($amount) {
            $updates['amount_approved'] = $amount;
        }

        $this->update($updates);
    }

    public function fund()
    {
        $this->update([
            'status' => 'funded',
            'funded_at' => now()
        ]);
    }

    public function reject()
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_at' => now()
        ]);
    }

    // Gestion du numéro de demande
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($funding) {
            if (!$funding->request_number) {
                $funding->request_number = static::generateRequestNumber();
            }
        });
    }

    public static function generateRequestNumber()
    {
        $prefix = 'BHDM-REQ-';
        $datePart = date('Ymd');
        $lastRequest = static::where('request_number', 'like', $prefix . $datePart . '-%')
            ->orderBy('request_number', 'desc')
            ->first();

        if ($lastRequest) {
            $lastNumber = intval(substr($lastRequest->request_number, -4));
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $datePart . '-' . $nextNumber;
    }
}