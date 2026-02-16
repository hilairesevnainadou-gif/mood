<?php

namespace App\Models;

use App\Models\FundingDocument;
use App\Models\FundingType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundingRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'request_number',
        'title',
        'is_predefined',
        'funding_type_id',
        'type',
        'amount_requested',
        'amount_approved',
        'total_repayment_amount',
        'monthly_repayment_amount',
        'repayment_duration_months',
        'repayment_start_date',
        'repayment_end_date',
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
        'paid_at',
        'validated_at',
        'validated_by',
        'admin_validation_notes',
        'expected_payment',
        'payment_motif',
        'local_committee_country',
        // Champs Kkiapay
        'kkiapay_transaction_id',
        'kkiapay_phone',
        'kkiapay_amount_paid',
        // Champs transfert
        'documents_checked_at',
        'documents_checked_by',
        'transfer_scheduled_at',
        'transfer_executed_at',
        'transfer_status',
        'transfer_cancellation_reason',
        'final_notes',
        'credited_at',
    ];

    protected $casts = [
        'amount_requested' => 'decimal:2',
        'amount_approved' => 'decimal:2',
        'expected_payment' => 'decimal:2',
        'kkiapay_amount_paid' => 'decimal:2',
        'total_repayment_amount' => 'decimal:2',
        'monthly_repayment_amount' => 'decimal:2',
        'tps_estimated' => 'decimal:4',
        'tps_final' => 'decimal:4',
        'is_predefined' => 'boolean',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'committee_review_started_at' => 'datetime',
        'committee_decision_at' => 'datetime',
        'approved_at' => 'datetime',
        'funded_at' => 'datetime',
        'paid_at' => 'datetime',
        'validated_at' => 'datetime',
        'documents_checked_at' => 'datetime',
        'transfer_scheduled_at' => 'datetime',
        'transfer_executed_at' => 'datetime',
        'repayment_start_date' => 'date',
        'repayment_end_date' => 'date',
        'credited_at' => 'datetime',
    ];

    // ==================== RELATIONS ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fundingType()
    {
        return $this->belongsTo(FundingType::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function documentsCheckedBy()
    {
        return $this->belongsTo(User::class, 'documents_checked_by');
    }

    public function documents()
    {
        return $this->hasMany(FundingDocument::class);
    }

    public function repayments()
    {
        return $this->hasMany(FundingRepayment::class);
    }

    // ==================== ACCESSORS ====================

    public function getFormattedAmountRequestedAttribute(): string
    {
        return number_format($this->amount_requested, 0, ',', ' ') . ' FCFA';
    }

    public function getFormattedAmountApprovedAttribute(): ?string
    {
        return $this->amount_approved
            ? number_format($this->amount_approved, 0, ',', ' ') . ' FCFA'
            : null;
    }

    public function getFormattedExpectedPaymentAttribute(): ?string
    {
        return $this->expected_payment
            ? number_format($this->expected_payment, 0, ',', ' ') . ' FCFA'
            : null;
    }

    public function getFormattedTotalRepaymentAttribute(): ?string
    {
        return $this->total_repayment_amount
            ? number_format($this->total_repayment_amount, 0, ',', ' ') . ' FCFA'
            : null;
    }

    public function getFormattedMonthlyRepaymentAttribute(): ?string
    {
        return $this->monthly_repayment_amount
            ? number_format($this->monthly_repayment_amount, 0, ',', ' ') . ' FCFA'
            : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Brouillon',
            'submitted' => 'Soumis',
            'under_review' => 'En examen',
            'pending_committee' => 'Comité local',
            'validated' => 'Validé (attente paiement)',
            'pending_payment' => 'Paiement requis',
            'paid' => 'Payé',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            'funded' => 'Financé',
            'in_progress' => 'En cours',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
            'documents_validated' => 'Docs validés (attente transfert)',
            'transfer_pending' => 'Transfert programmé',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'submitted' => 'blue',
            'under_review' => 'yellow',
            'pending_committee' => 'orange',
            'validated' => 'cyan',
            'pending_payment' => 'amber',
            'paid' => 'emerald',
            'approved' => 'green',
            'rejected' => 'red',
            'funded' => 'purple',
            'in_progress' => 'indigo',
            'completed' => 'teal',
            'cancelled' => 'red',
            'documents_validated' => 'pink',
            'transfer_pending' => 'violet',
            default => 'gray',
        };
    }

    public function getStatusClassAttribute(): string
    {
        $colors = [
            'gray' => 'bg-gray-100 text-gray-800',
            'blue' => 'bg-blue-100 text-blue-800',
            'yellow' => 'bg-yellow-100 text-yellow-800',
            'orange' => 'bg-orange-100 text-orange-800',
            'cyan' => 'bg-cyan-100 text-cyan-800',
            'amber' => 'bg-amber-100 text-amber-800',
            'emerald' => 'bg-emerald-100 text-emerald-800',
            'green' => 'bg-green-100 text-green-800',
            'red' => 'bg-red-100 text-red-800',
            'purple' => 'bg-purple-100 text-purple-800',
            'indigo' => 'bg-indigo-100 text-indigo-800',
            'teal' => 'bg-teal-100 text-teal-800',
            'pink' => 'bg-pink-100 text-pink-800',
            'violet' => 'bg-violet-100 text-violet-800',
        ];

        return $colors[$this->status_color] ?? $colors['gray'];
    }

    // ==================== SCOPES ====================

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'submitted', 'under_review', 'pending_committee']);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeNeedsPayment($query)
    {
        return $query->whereIn('status', ['validated', 'pending_payment']);
    }

    public function scopePendingTransfer($query)
    {
        return $query->whereIn('status', ['documents_validated', 'transfer_pending'])
            ->where('transfer_status', '!=', 'completed');
    }

    public function scopeReadyForTransfer($query)
    {
        return $query->where('status', 'documents_validated')
            ->where('transfer_status', 'scheduled');
    }

    // ==================== MÉTHODES ====================

    public function canBeEdited(): bool
    {
        return $this->status === 'draft';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid' && !empty($this->kkiapay_transaction_id);
    }

    public function requiresPayment(): bool
    {
        return in_array($this->status, ['validated', 'pending_payment'])
            && $this->expected_payment > 0;
    }

    public function isTransferPending(): bool
    {
        return in_array($this->status, ['documents_validated', 'transfer_pending'])
            && $this->transfer_status === 'scheduled';
    }

    public function canExecuteTransfer(): bool
    {
        return in_array($this->status, ['documents_validated', 'approved'])
            && $this->transfer_scheduled_at !== null
            && $this->transfer_executed_at === null
            && ($this->transfer_status === null || $this->transfer_status !== 'completed');
    }

    /**
     * Calcule le montant mensuel de remboursement
     */
    public function calculateMonthlyRepayment(): float
    {
        if (!$this->total_repayment_amount || !$this->repayment_duration_months || $this->repayment_duration_months <= 0) {
            return 0;
        }
        return $this->total_repayment_amount / $this->repayment_duration_months;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_number)) {
                $model->request_number = static::generateRequestNumber();
            }
        });
    }

    public static function generateRequestNumber(): string
    {
        $prefix = 'BHDM-' . date('Ymd');
        $last = static::where('request_number', 'like', $prefix . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $num = $last ? intval(substr($last->request_number, -4)) + 1 : 1;

        return $prefix . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
