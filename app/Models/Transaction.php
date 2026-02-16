<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'transaction_id',
        'reference',
        'type',
        'amount',
        'fee',
        'total_amount',
        'payment_method',
        'status',
        'description',
        'metadata',
        'completed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'metadata' => 'array',
        'completed_at' => 'datetime'
    ];

    /**
     * Relation avec le wallet
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

      /**
     * Labels pour les types
     */
    public function getTypeLabel(): string
    {
        $labels = [
            'credit' => 'Dépôt',
            'debit' => 'Retrait',
            'transfer' => 'Transfert',
            'payment' => 'Paiement',
            'refund' => 'Remboursement',
            'fee' => 'Frais',
        ];

        return $labels[$this->type] ?? $this->type;
    }

    /**
     * Labels pour les statuts
     */
    public function getStatusLabel(): string
    {
        $labels = [
            'pending' => 'En attente',
            'processing' => 'En cours',
            'completed' => 'Complété',
            'failed' => 'Échoué',
            'cancelled' => 'Annulé',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Relation avec l'utilisateur (via le wallet)
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Wallet::class,
            'id',           // Clé locale sur wallets
            'id',           // Clé locale sur users
            'wallet_id',    // Clé étrangère sur transactions
            'user_id'       // Clé étrangère sur wallets
        );
    }

    // Accessors pour l'affichage
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0, ',', ' ') . ' XOF';
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total_amount, 0, ',', ' ') . ' XOF';
    }

    public function getFormattedFeeAttribute(): string
    {
        return number_format($this->fee, 0, ',', ' ') . ' XOF';
    }

    /**
     * Icône selon le type de transaction
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'deposit' => 'fa-arrow-down',
            'withdrawal' => 'fa-arrow-up',
            'transfer' => 'fa-exchange-alt',
            'payment' => 'fa-credit-card',
            default => 'fa-circle',
        };
    }

    /**
     * Label lisible pour le type
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'deposit' => 'Dépôt',
            'withdrawal' => 'Retrait',
            'transfer' => 'Transfert',
            'payment' => 'Paiement',
            default => ucfirst($this->type),
        };
    }

    /**
     * Icône selon le statut
     */
    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            'pending' => 'fa-clock',
            'completed' => 'fa-check',
            'failed' => 'fa-xmark',
            'cancelled' => 'fa-ban',
            default => 'fa-circle',
        };
    }

    /**
     * Label lisible pour le statut
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'completed' => 'Validée',
            'failed' => 'Échouée',
            'cancelled' => 'Annulée',
            default => ucfirst($this->status),
        };
    }

    // Scopes
    public function scopeDeposits($query)
    {
        return $query->where('type', 'deposit');
    }

    public function scopeWithdrawals($query)
    {
        return $query->where('type', 'withdrawal');
    }

    public function scopeTransfers($query)
    {
        return $query->where('type', 'transfer');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Méthodes de vérification
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Calcule le montant total (montant + frais)
     */
    public function calculateTotal(): float
    {
        return $this->amount + ($this->fee ?? 0);
    }

    /**
     * Met à jour le statut et enregistre la date de complétion
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Met à jour le statut comme échoué
     */
    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'metadata' => array_merge($this->metadata ?? [], ['failure_reason' => $reason]),
        ]);
    }
}

