<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'profile_photo',
        'birth_date',
        'gender',
        'address',
        'city',
        'country',
        'postal_code',
        'company_name',
        'company_type',
        'sector',
        'job_title',
        'employees_count',
        'annual_turnover',
        'member_id',
        'member_since',
        'member_status',
        'member_type',
        'is_active',
        'is_verified',
        'is_admin',
        'is_moderator',
        'accepts_newsletter',
        'accepts_notifications',
        'last_login_at',
        'last_login_ip',
        'last_login_device',
        'settings',
        'preferences',
        'metadata',
        'project_name',
        'project_type',
        'project_description',
        'funding_needed',
        'expected_jobs',
        'project_duration',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'member_since' => 'date',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'is_admin' => 'boolean',
        'is_moderator' => 'boolean',
        'accepts_newsletter' => 'boolean',
        'accepts_notifications' => 'boolean',
        'employees_count' => 'integer',
        'annual_turnover' => 'decimal:2',
        'settings' => 'array',
        'preferences' => 'array',
        'metadata' => 'array'
    ];

    // Accessors
    public function getFullNameAttribute(): string
    {
        return $this->first_name && $this->last_name
            ? $this->first_name . ' ' . $this->last_name
            : ($this->name ?? 'Utilisateur');
    }

    public function getInitialsAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
        }
        return strtoupper(substr($this->name ?? 'U', 0, 2));
    }
 /**
     * Send the email verification notification.
     * OVERRIDE de la méthode par défaut de MustVerifyEmail
     */
    public function sendEmailVerificationNotification(): void
    {
        // Envoyer via notre Mailable personnalisée au lieu de la notification par défaut
        Mail::to($this->email)->send(new CustomVerifyEmail($this));
    }

    /**
     * Get the e-mail address where password reset links are sent.
     */
    public function getEmailForVerification(): string
    {
        return $this->email;
    }

    /**
     * Accessor pour l'URL de la photo de profil
     * CORRIGÉ : Le fichier est stocké dans storage/app/public/profiles/
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo) {
            // Vérifier si le fichier existe dans le dossier profiles/
            if (Storage::disk('public')->exists('profiles/' . $this->profile_photo)) {
                return asset('storage/profiles/' . $this->profile_photo);
            }
        }
        
        // Fallback vers UI Avatars avec le nom complet
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&background=random&color=fff&size=128';
    }

    // Relations
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class)->latest();
    }

    public function fundingRequests()
    {
        return $this->hasMany(FundingRequest::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function supportMessages()
    {
        return $this->hasMany(SupportMessage::class);
    }

    public function assignedTickets()
    {
        return $this->hasMany(SupportTicket::class, 'assigned_to');
    }

    public function progress()
    {
        return $this->hasOne(UserProgress::class);
    }

    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }

    /**
     * Relation transactions corrigée - évite les problèmes de requête
     */
    public function transactions()
    {
        return $this->hasManyThrough(
            Transaction::class,
            Wallet::class,
            'user_id',      // Clé étrangère sur wallets
            'wallet_id',    // Clé étrangère sur transactions
            'id',           // Clé locale sur users
            'id'            // Clé locale sur wallets
        );
    }

    /**
     * Vérifie si l'utilisateur a des transactions en attente
     * Correction du problème de guillemets dans la requête
     */
    public function hasPendingTransactions(): bool
    {
        return $this->transactions()
            ->where('transactions.status', 'pending') // Table prefixée pour éviter ambiguïté
            ->exists();
    }

    /**
     * Vérifie si l'utilisateur a des demandes de financement en cours
     */
    public function hasPendingFundingRequests(): bool
    {
        return $this->fundingRequests()
            ->whereIn('status', ['pending', 'processing', 'under_review'])
            ->exists();
    }

    /**
     * Vérifie si l'utilisateur a des opérations en cours (transactions OU financement)
     */
    public function hasPendingOperations(): bool
    {
        return $this->hasPendingTransactions() || $this->hasPendingFundingRequests();
    }

    public function committeeDecisions()
    {
        return $this->hasManyThrough(CommitteeDecision::class, FundingRequest::class);
    }

    // Formations
    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'training_user', 'user_id', 'training_id')
            ->withPivot('enrolled_at', 'completed_at', 'status', 'progress', 'certificate_id')
            ->withTimestamps();
    }

    public function settings()
    {
        return $this->hasOne(Setting::class);
    }

    public function requests()
    {
        return $this->hasMany(FundingRequest::class);
    }

    public function fundingDocuments()
    {
        return $this->hasMany(FundingDocument::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    public function scopeClients($query)
    {
        return $query->where('is_admin', false)->where('is_moderator', false);
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    public function scopeParticuliers($query)
    {
        return $query->where('member_type', 'particulier');
    }

    public function scopeEntreprises($query)
    {
        return $query->where('member_type', 'entreprise');
    }

    // Méthodes de vérification
    public function isAdministrator(): bool
    {
        return $this->is_admin === true;
    }

    public function isModerator(): bool
    {
        return $this->is_moderator === true;
    }

    public function isClient(): bool
    {
        return !$this->is_admin && !$this->is_moderator;
    }

    public function isParticulier(): bool
    {
        return $this->member_type === 'particulier';
    }

    public function isEntreprise(): bool
    {
        return $this->member_type === 'entreprise';
    }

    public function hasPermission($permission): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        if ($this->isModerator()) {
            $moderatorPermissions = ['view_users', 'edit_users', 'view_requests'];
            return in_array($permission, $moderatorPermissions);
        }

        $clientPermissions = [
            'view_profile',
            'edit_profile',
            'view_wallet',
            'create_request',
            'view_documents',
            'upload_documents',
            'view_trainings',
            'submit_support'
        ];

        return in_array($permission, $clientPermissions);
    }

    // Méthodes utilitaires
    public function generateMemberId(): string
    {
        if (!$this->member_id) {
            $year = date('Y');
            $sequence = str_pad($this->id, 6, '0', STR_PAD_LEFT);
            $this->member_id = 'BHDM-' . $year . '-' . $sequence;
            $this->save();
        }
        return $this->member_id;
    }

    public function recordLogin($request): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'last_login_device' => $request->userAgent()
        ]);
    }

    public function getCompletionPercentage(): int
    {
        $profileFields = [
            'first_name',
            'last_name',
            'email',
            'phone',
            'birth_date',
            'address',
            'city',
            'country',
        ];

        $completedFields = collect($profileFields)->filter(fn($field) => !empty($this->$field))->count();

        return (int) round(($completedFields / count($profileFields)) * 100);
    }

    // Méthodes formations
    public function getEnrolledTrainings()
    {
        return $this->trainings()
            ->wherePivot('status', 'enrolled')
            ->orderBy('training_user.enrolled_at', 'desc')
            ->get();
    }

    public function getCompletedTrainings()
    {
        return $this->trainings()
            ->wherePivot('status', 'completed')
            ->orderBy('training_user.completed_at', 'desc')
            ->get();
    }

    public function isEnrolledInTraining($trainingId): bool
    {
        return $this->trainings()
            ->where('training_id', $trainingId)
            ->wherePivot('status', 'enrolled')
            ->exists();
    }

    public function getTrainingProgress($trainingId): int
    {
        $training = $this->trainings()
            ->where('training_id', $trainingId)
            ->first();

        return $training ? (int) $training->pivot->progress : 0;
    }

    // Méthodes documents
    public function hasAllRequiredDocuments(): bool
    {
        $missing = Document::getMissingRequiredDocuments($this->id, $this->member_type);
        return empty($missing);
    }

    public function hasUploadedRequiredDocuments(): bool
    {
        $missing = Document::getMissingUploadedRequiredDocuments($this->id, $this->member_type);
        return empty($missing);
    }

    public function getMissingRequiredDocuments()
    {
        return Document::getMissingRequiredDocuments($this->id, $this->member_type);
    }

    public function getMissingUploadedRequiredDocuments()
    {
        return Document::getMissingUploadedRequiredDocuments($this->id, $this->member_type);
    }
}