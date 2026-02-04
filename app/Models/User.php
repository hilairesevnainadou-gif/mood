<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
    public function getFullNameAttribute()
    {
        return $this->first_name && $this->last_name
            ? $this->first_name . ' ' . $this->last_name
            : $this->name;
    }

    public function getInitialsAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
        }
        return strtoupper(substr($this->name, 0, 2));
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&color=1b5a8d&background=e9ecef';
    }

    // Relations
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
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

    public function progress()
    {
        return $this->hasOne(UserProgress::class);
    }

    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Wallet::class);
    }

    public function committeeDecisions()
    {
        return $this->hasManyThrough(CommitteeDecision::class, FundingRequest::class);
    }

    // AJOUTEZ CETTE RELATION - Formations
    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'training_user', 'user_id', 'training_id')
            ->withPivot('enrolled_at', 'completed_at', 'status', 'progress', 'certificate_id')
            ->withTimestamps();
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
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
        return $query->where('is_admin', false);
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

    // Méthodes
    public function isAdministrator()
    {
        return $this->is_admin === true;
    }

    public function isModerator()
    {
        return $this->is_moderator === true;
    }

    public function isClient()
    {
        return !$this->is_admin && !$this->is_moderator;
    }

    public function isParticulier()
    {
        return $this->member_type === 'particulier';
    }

    public function isEntreprise()
    {
        return $this->member_type === 'entreprise';
    }

    public function hasPermission($permission)
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

    public function generateMemberId()
    {
        if (!$this->member_id) {
            $year = date('Y');
            $sequence = str_pad($this->id, 6, '0', STR_PAD_LEFT);
            $this->member_id = 'BHDM-' . $year . '-' . $sequence;
            $this->save();
        }
        return $this->member_id;
    }

    public function recordLogin($request)
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'last_login_device' => $request->userAgent()
        ]);
    }

    public function getCompletionPercentage()
    {
        $profileFields = [
            'first_name',
            'last_name',
            'email',
            'phone',
            'profile_photo',
            'birth_date',
            'gender',
            'address',
            'city',
            'country',
            'postal_code',
            'company_name',
            'job_title'
        ];

        $completedFields = 0;
        foreach ($profileFields as $field) {
            if (!empty($this->$field)) {
                $completedFields++;
            }
        }

        return round(($completedFields / count($profileFields)) * 100);
    }

    // AJOUTEZ CETTE MÉTHODE - Récupérer les formations en cours
    public function getEnrolledTrainings()
    {
        return $this->trainings()
            ->wherePivot('status', 'enrolled')
            ->orderBy('training_user.enrolled_at', 'desc')
            ->get();
    }

    // AJOUTEZ CETTE MÉTHODE - Récupérer les formations complétées
    public function getCompletedTrainings()
    {
        return $this->trainings()
            ->wherePivot('status', 'completed')
            ->orderBy('training_user.completed_at', 'desc')
            ->get();
    }

    // AJOUTEZ CETTE MÉTHODE - Vérifier si l'utilisateur est inscrit à une formation
    public function isEnrolledInTraining($trainingId)
    {
        return $this->trainings()
            ->where('training_id', $trainingId)
            ->wherePivot('status', 'enrolled')
            ->exists();
    }

    // AJOUTEZ CETTE MÉTHODE - Récupérer la progression d'une formation
    public function getTrainingProgress($trainingId)
    {
        $training = $this->trainings()
            ->where('training_id', $trainingId)
            ->first();

        return $training ? $training->pivot->progress : 0;
    }


    public function assignedTickets()
    {
        return $this->hasMany(SupportTicket::class, 'assigned_to');
    }

    // Relation pour les paramètres
    public function settings()
    {
        return $this->hasOne(Setting::class);
    }

    // Relation pour les requêtes de financement
    public function requests()
    {
        return $this->hasMany(FundingRequest::class);
    }


public function fundingDocuments()
{
    return $this->hasMany(FundingDocument::class);
}

// Méthode pour vérifier si l'utilisateur a tous les documents requis
public function hasAllRequiredDocuments()
{
    $missing = Document::getMissingRequiredDocuments($this->id, $this->member_type);
    return empty($missing);
}

// Méthode pour obtenir les documents manquants
public function getMissingRequiredDocuments()
{
    return Document::getMissingRequiredDocuments($this->id, $this->member_type);
}
}
