<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wallet_number',
        'balance',
        'currency',
        'status',
        'pin_hash',
        'activated_at',
        'last_transaction_at'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'activated_at' => 'datetime',
        'last_transaction_at' => 'datetime'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Accessors
    public function getFormattedBalanceAttribute()
    {
        return number_format($this->balance, 2, ',', ' ') . ' ' . $this->currency;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Méthodes
    public function canWithdraw($amount)
    {
        return $this->balance >= $amount;
    }

    public function deposit($amount, $paymentMethod, $metadata = [])
    {
        return $this->transactions()->create([
            'transaction_id' => \Illuminate\Support\Str::uuid(),
            'type' => 'deposit',
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'status' => 'completed',
            'metadata' => $metadata
        ]);
    }

    public function withdraw($amount, $withdrawalMethod, $metadata = [])
    {
        return $this->transactions()->create([
            'transaction_id' => \Illuminate\Support\Str::uuid(),
            'type' => 'withdrawal',
            'amount' => $amount,
            'payment_method' => $withdrawalMethod,
            'status' => 'completed',
            'metadata' => $metadata
        ]);
    }
}