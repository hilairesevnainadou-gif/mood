<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'security_level',
        'activated_at',
        'last_transaction_at'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'activated_at' => 'datetime',
        'last_transaction_at' => 'datetime'
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class)->latest();
    }

    /**
     * Relation avec l'historique des opérations
     */
    public function histories(): HasMany
    {
        return $this->hasMany(WalletHistory::class)->latest();
    }

    // Accessors
    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 2, ',', ' ') . ' ' . $this->currency;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Méthodes
    public function canWithdraw($amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Crée une transaction de dépôt et crédite immédiatement
     */
    public function depositAndCredit($amount, $paymentMethod = 'kkiapay', $metadata = []): Transaction
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($amount, $paymentMethod, $metadata) {
            // Créer la transaction
            $transaction = $this->transactions()->create([
                'transaction_id' => \Illuminate\Support\Str::uuid(),
                'reference' => 'DEP-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6)),
                'type' => 'credit',
                'amount' => $amount,
                'total_amount' => $amount,
                'fee' => 0,
                'payment_method' => $paymentMethod,
                'status' => 'completed', // Directement complété
                'description' => 'Dépôt via ' . $paymentMethod,
                'metadata' => $metadata,
                'completed_at' => now(),
                'paid_at' => now()
            ]);

            // Créditer immédiatement
            $this->increment('balance', $amount);
            $this->update(['last_transaction_at' => now()]);

            // Créer l'historique
            $this->histories()->create([
                'type' => 'credit',
                'amount' => $amount,
                'description' => 'Dépôt via ' . $paymentMethod,
                'transaction_id' => $transaction->id
            ]);

            return $transaction;
        });
    }

    public function deposit($amount, $paymentMethod, $metadata = [])
    {
        return $this->transactions()->create([
            'transaction_id' => \Illuminate\Support\Str::uuid(),
            'type' => 'deposit',
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'status' => 'pending', // En attente de confirmation
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
            'status' => 'pending',
            'metadata' => $metadata
        ]);
    }
}
