<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundingPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'funding_request_id',
        'payment_number',
        'payment_motif',           // AJOUTÉ : Code 4 chiffres (ex: 7392)
        'amount',
        'approved_amount',         // AJOUTÉ : Montant subvention pour demandes custom
        'payment_date',
        'payment_method',
        'type',                    // AJOUTÉ : registration/transfer_fee/additional
        'status',
        'reference',
        'transaction_id',
        'bank_name',
        'account_number',
        'mobile_operator',
        'phone_number',
        'country',                 // AJOUTÉ : Pays pour filtrer opérateurs
        'comments',
        'admin_notes',             // AJOUTÉ : Notes de validation admin
        'metadata',
        'processed_at',
        'completed_at',
        'failed_at',
        'created_by',
        'verified_by',             // AJOUTÉ : ID admin qui a validé
        'verified_at',
        'payment_date',        // ✅ Doit être présent
        'payment_method',     // AJOUTÉ : Date validation admin
        'confirmed_by_user_at'     // AJOUTÉ : Date confirmation client
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',      // AJOUTÉ
        'payment_date' => 'date',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'verified_at' => 'datetime',           // AJOUTÉ
        'confirmed_by_user_at' => 'datetime',  // AJOUTÉ
        'metadata' => 'array'
    ];

    // Relations
    public function funding()
    {
        return $this->belongsTo(Funding::class, 'funding_request_id');
    }

    public function fundingRequest()
    {
        return $this->belongsTo(FundingRequest::class, 'funding_request_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // AJOUTÉ : Relation avec l'admin qui a vérifié
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Accessors conservés + nouveau pour le motif
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 0, ',', ' ') . ' FCFA';
    }

    // AJOUTÉ : Accessor pour afficher le motif formaté
    public function getFormattedMotifAttribute()
    {
        return $this->payment_motif ? sprintf('%04d', $this->payment_motif) : 'N/A';
    }

    // AJOUTÉ : Label pour le type de paiement
    public function getTypeLabelAttribute()
    {
        $labels = [
            'registration' => 'Frais d\'inscription',
            'transfer_fee' => 'Frais de transfert urgent',
            'additional' => 'Frais supplémentaires'
        ];
        return $labels[$this->type] ?? $this->type;
    }

    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            'bank_transfer' => 'Virement bancaire',
            'mobile_money' => 'Mobile Money',
            'cash' => 'Espèces',
            'cheque' => 'Chèque',
            'other' => 'Autre'
        ];

        return $labels[$this->payment_method] ?? $this->payment_method;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'En attente de paiement',
            'processing' => 'En attente de vérification',  // Modifié pour clarté
            'completed' => 'Paiement confirmé',
            'failed' => 'Échoué/rejeté',
            'cancelled' => 'Annulé',
            'refunded' => 'Remboursé'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    // AJOUTÉ : Vérification si en attente de validation admin
    public function getIsProcessingAttribute()
    {
        return $this->status === 'processing';
    }

    // Méthodes d'action modifiées et nouvelles

    /**
     * Client confirme avoir payé (via USSD)
     */
    public function confirmByUser($operatorName, $phoneUsed, $transactionId = null)
    {
        $this->update([
            'status' => 'processing',
            'mobile_operator' => $operatorName,
            'phone_number' => $phoneUsed,
            'transaction_id' => $transactionId,
            'confirmed_by_user_at' => now()
        ]);
    }

    /**
     * Admin valide le paiement après vérification
     */
    public function validateByAdmin($adminUserId, $notes = null)
    {
        $this->update([
            'status' => 'completed',
            'verified_by' => $adminUserId,
            'verified_at' => now(),
            'completed_at' => now(),
            'admin_notes' => $notes
        ]);
    }

    /**
     * Admin rejette le paiement (fraude ou non reçu)
     */
    public function rejectByAdmin($adminUserId, $reason)
    {
        $this->update([
            'status' => 'failed',
            'verified_by' => $adminUserId,
            'verified_at' => now(),
            'failed_at' => now(),
            'admin_notes' => $reason
        ]);
    }

    // Méthodes legacy conservées pour compatibilité
    public function markAsProcessing()
    {
        $this->update([
            'status' => 'processing',
            'processed_at' => now()
        ]);
    }

    public function markAsCompleted($transactionId = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'transaction_id' => $transactionId ?? $this->transaction_id
        ]);
    }

    public function markAsFailed()
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now()
        ]);
    }

    // Génération du numéro de paiement
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (!$payment->payment_number) {
                $payment->payment_number = static::generatePaymentNumber();
            }

            // AJOUTÉ : Génération automatique du motif à 4 chiffres si non fourni
            if (!$payment->payment_motif && $payment->type === 'registration') {
                $payment->payment_motif = static::generateUniqueMotif();
            }
        });
    }

    public static function generatePaymentNumber()
    {
        $prefix = 'BHDM-PAY-';
        $datePart = date('Ymd');
        $lastPayment = static::where('payment_number', 'like', $prefix . $datePart . '-%')
            ->orderBy('payment_number', 'desc')
            ->first();

        if ($lastPayment) {
            $lastNumber = intval(substr($lastPayment->payment_number, -4));
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $datePart . '-' . $nextNumber;
    }

    // AJOUTÉ : Génération du motif unique à 4 chiffres
    public static function generateUniqueMotif()
    {
        do {
            $motif = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('payment_motif', $motif)
                      ->where('status', 'pending')
                      ->exists());

        return $motif;
    }

    public function fundingRequest() {
        return $this->belongsTo(FundingRequest::class);
    }

}
