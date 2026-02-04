<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'funding_request_id',
        'user_id',
        'repayment_number',
        'amount',
        'tps_amount',
        'capital_amount',
        'due_date',
        'status',
        'paid_at',
        'payment_method',
        'transaction_reference',
        'installment_number',
        'total_installments'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tps_amount' => 'decimal:2',
        'capital_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime'
    ];

    // Relations
    public function fundingRequest()
    {
        return $this->belongsTo(FundingRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 0, ',', ' ') . ' FCFA';
    }

    public function getFormattedCapitalAttribute()
    {
        return number_format($this->capital_amount, 0, ',', ' ') . ' FCFA';
    }

    public function getFormattedTpsAttribute()
    {
        return number_format($this->tps_amount, 0, ',', ' ') . ' FCFA';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'En attente',
            'paid' => 'Payé',
            'overdue' => 'En retard',
            'cancelled' => 'Annulé'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    // Méthodes
    public function isOverdue()
    {
        return $this->due_date < now() && $this->status === 'pending';
    }

    public function markAsPaid($paymentMethod, $transactionReference)
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
            'transaction_reference' => $transactionReference
        ]);
    }
}