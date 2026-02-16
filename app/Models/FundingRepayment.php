<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundingRepayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'funding_request_id',
        'repayment_number',
        'due_date',
        'amount_due',
        'amount_paid',
        'status',
        'paid_at',
        'transaction_id',
        'payment_method',
        'late_fees',
        'notes',
        'reminder_sent_at',
    ];

    protected $casts = [
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'late_fees' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    // ==================== RELATIONS ====================

    public function fundingRequest()
    {
        return $this->belongsTo(FundingRequest::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // ==================== ACCESSORS ====================

    public function getFormattedAmountDueAttribute(): string
    {
        return number_format($this->amount_due, 0, ',', ' ') . ' FCFA';
    }

    public function getFormattedAmountPaidAttribute(): string
    {
        return number_format($this->amount_paid ?? 0, 0, ',', ' ') . ' FCFA';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'paid' => 'Payé',
            'partial' => 'Partiel',
            'late' => 'En retard',
            'cancelled' => 'Annulé',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'paid' => 'green',
            'partial' => 'orange',
            'late' => 'red',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }

    // ==================== MÉTHODES ====================

    public function isPaid(): bool
    {
        return $this->status === 'paid' && $this->amount_paid >= $this->amount_due;
    }

    public function isLate(): bool
    {
        return $this->due_date < now()->startOfDay() && !$this->isPaid();
    }

    public function markAsPaid(float $amount, ?string $transactionId = null, ?string $paymentMethod = null): void
    {
        $this->update([
            'amount_paid' => $amount,
            'status' => $amount >= $this->amount_due ? 'paid' : 'partial',
            'paid_at' => now(),
            'transaction_id' => $transactionId,
            'payment_method' => $paymentMethod,
        ]);
    }

    public function markAsLate(): void
    {
        if ($this->status === 'pending' && $this->isLate()) {
            $this->update(['status' => 'late']);
        }
    }

    // ==================== BOOT ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->repayment_number)) {
                $model->repayment_number = static::generateRepaymentNumber();
            }
        });
    }

    public static function generateRepaymentNumber(): string
    {
        $prefix = 'REM-' . date('Ym');
        $last = static::where('repayment_number', 'like', $prefix . '-%')
                      ->orderBy('id', 'desc')
                      ->first();

        $num = $last ? intval(substr($last->repayment_number, -4)) + 1 : 1;

        return $prefix . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
