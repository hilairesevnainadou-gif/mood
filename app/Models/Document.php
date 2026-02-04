<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'funding_request_id',
        'name',
        'type',
        'category',
        'is_profile_document',
        'is_required',
        'path',
        'mime_type',
        'size',
        'original_filename',
        'description',
        'status',
        'rejection_reason',
        'validated_at',
        'validated_by',
        'expiry_date',
        'is_expired',
        'uploaded_at'
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'expiry_date' => 'date',
        'uploaded_at' => 'datetime',
        'size' => 'integer',
        'is_profile_document' => 'boolean',
        'is_required' => 'boolean',
        'is_expired' => 'boolean'
    ];

    protected $appends = [
        'file_url',
        'formatted_size',
        'type_label',
        'status_label',
        'is_expired_status'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fundingRequest()
    {
        return $this->belongsTo(FundingRequest::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // Scopes
    public function scopeProfileDocuments($query)
    {
        return $query->where('is_profile_document', true)
            ->whereNull('funding_request_id');
    }

    public function scopeFundingDocuments($query)
    {
        return $query->where('is_profile_document', false)
            ->whereNotNull('funding_request_id');
    }

    public function scopeForFundingRequest($query, $requestId)
    {
        return $query->where('funding_request_id', $requestId)
            ->where('is_profile_document', false);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeValidated($query)
    {
        return $query->where('status', 'validated');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeNotExpired($query)
    {
        return $query->where('is_expired', false)
            ->orWhereNull('expiry_date');
    }

    public function scopeExpired($query)
    {
        return $query->where('is_expired', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecentFirst($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Accessors
    public function getFileUrlAttribute()
    {
        if ($this->path && Storage::disk('public')->exists($this->path)) {
            return Storage::disk('public')->url($this->path);
        }
        return null;
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;

        if ($bytes == 0) return '0 Bytes';

        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));

        return number_format($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    public function getTypeLabelAttribute()
    {
        $labels = [
            'identity' => 'Pièce d\'identité',
            'passport' => 'Passeport',
            'driver_license' => 'Permis de conduire',
            'cni' => 'Carte Nationale d\'Identité',
            'business_plan' => 'Business Plan',
            'financial_statements' => 'États financiers',
            'tax_document' => 'Document fiscal',
            'legal_document' => 'Document légal',
            'project_photos' => 'Photos du projet',
            'proof_address' => 'Justificatif de domicile',
            'proof_income' => 'Justificatif de revenus',
            'bank_statement' => 'Relevé bancaire',
            'certificate' => 'Certificat',
            'diploma' => 'Diplôme',
            'other' => 'Autre'
        ];

        return $labels[$this->type] ?? $this->type;
    }

    public function getCategoryLabelAttribute()
    {
        $labels = [
            'personal' => 'Personnel',
            'business' => 'Entreprise',
            'financial' => 'Financier',
            'project' => 'Projet',
            'verification' => 'Vérification',
            'education' => 'Éducation',
            'other' => 'Autre'
        ];

        return $labels[$this->category] ?? $this->category;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'En attente',
            'validated' => 'Validé',
            'rejected' => 'Rejeté'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'validated' => 'success',
            'rejected' => 'danger'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getIsExpiredStatusAttribute()
    {
        if (!$this->expiry_date) return false;

        if ($this->is_expired === null) {
            $this->checkExpiry();
        }

        return $this->is_expired;
    }

    public function getIsProfileDocAttribute()
    {
        return $this->is_profile_document && is_null($this->funding_request_id);
    }

    public function getIsFundingDocAttribute()
    {
        return !$this->is_profile_document && !is_null($this->funding_request_id);
    }

    public function getFileIconAttribute()
    {
        if ($this->isImage()) return 'fas fa-file-image';
        if ($this->isPdf()) return 'fas fa-file-pdf';
        if ($this->isWordDocument()) return 'fas fa-file-word';
        if ($this->isExcelDocument()) return 'fas fa-file-excel';
        if ($this->isPowerpointDocument()) return 'fas fa-file-powerpoint';
        if ($this->isArchive()) return 'fas fa-file-archive';

        return 'fas fa-file';
    }

    // Méthodes de vérification
    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isPdf()
    {
        return $this->mime_type === 'application/pdf' || str_contains($this->original_filename, '.pdf');
    }

    public function isWordDocument()
    {
        return str_contains($this->mime_type, 'word') ||
               str_contains($this->mime_type, 'document') ||
               str_contains($this->original_filename, '.doc') ||
               str_contains($this->original_filename, '.docx');
    }

    public function isExcelDocument()
    {
        return str_contains($this->mime_type, 'excel') ||
               str_contains($this->mime_type, 'spreadsheet') ||
               str_contains($this->original_filename, '.xls') ||
               str_contains($this->original_filename, '.xlsx');
    }

    public function isPowerpointDocument()
    {
        return str_contains($this->mime_type, 'powerpoint') ||
               str_contains($this->mime_type, 'presentation') ||
               str_contains($this->original_filename, '.ppt') ||
               str_contains($this->original_filename, '.pptx');
    }

    public function isArchive()
    {
        return str_contains($this->mime_type, 'zip') ||
               str_contains($this->mime_type, 'compressed') ||
               str_contains($this->original_filename, '.zip') ||
               str_contains($this->original_filename, '.rar') ||
               str_contains($this->original_filename, '.7z');
    }

    // Méthodes d'action
    public function validateDocument($validatorId = null, $comments = null)
    {
        $this->update([
            'status' => 'validated',
            'validated_at' => now(),
            'validated_by' => $validatorId,
            'rejection_reason' => $comments
        ]);

        return $this;
    }

    public function rejectDocument($reason, $validatorId = null)
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'validated_by' => $validatorId,
            'validated_at' => now()
        ]);

        return $this;
    }

    public function checkExpiry()
    {
        if ($this->expiry_date && now()->gt($this->expiry_date)) {
            $this->update(['is_expired' => true]);
            return true;
        }

        if ($this->expiry_date && now()->lt($this->expiry_date)) {
            $this->update(['is_expired' => false]);
        }

        return false;
    }

    public function markAsPending()
    {
        $this->update([
            'status' => 'pending',
            'validated_at' => null,
            'validated_by' => null,
            'rejection_reason' => null
        ]);

        return $this;
    }

    public function updateExpiryDate($date)
    {
        $this->update([
            'expiry_date' => $date,
            'is_expired' => $date && now()->gt($date)
        ]);

        return $this;
    }

    // Méthode pour supprimer le fichier physique
    public function deleteFile()
    {
        if ($this->path && Storage::disk('public')->exists($this->path)) {
            Storage::disk('public')->delete($this->path);
        }

        return $this;
    }

    // Méthodes statiques
    public static function getProfileDocumentTypes($memberType)
    {
        $requiredDocuments = RequiredDocument::getByMemberType($memberType, true);

        return $requiredDocuments->map(function ($doc) {
            return [
                'type' => $doc->document_type,
                'name' => $doc->name,
                'description' => $doc->description,
                'is_required' => $doc->is_required,
                'category' => $doc->category,
                'has_expiry_date' => $doc->has_expiry_date,
                'validity_days' => $doc->validity_days,
                'allowed_formats' => $doc->allowed_formats ?? [],
                'max_size_mb' => $doc->max_size_mb
            ];
        })->toArray();
    }

    public static function getMissingRequiredDocuments($userId, $memberType)
    {
        $requiredDocuments = RequiredDocument::getByMemberType($memberType, true);
        $missingDocuments = [];

        foreach ($requiredDocuments as $requiredDoc) {
            $exists = self::where('user_id', $userId)
                ->where('type', $requiredDoc->document_type)
                ->where('is_profile_document', true)
                ->where('status', 'validated')
                ->where(function ($query) use ($requiredDoc) {
                    if ($requiredDoc->has_expiry_date) {
                        $query->where(function($q) {
                                $q->whereNull('expiry_date')
                                  ->orWhere('expiry_date', '>', now());
                            })
                            ->where('is_expired', false);
                    }
                })
                ->exists();

            if (!$exists && $requiredDoc->is_required) {
                $missingDocuments[] = $requiredDoc;
            }
        }

        return $missingDocuments;
    }

    public static function getMissingUploadedRequiredDocuments($userId, $memberType)
    {
        $requiredDocuments = RequiredDocument::getByMemberType($memberType, true);
        $missingDocuments = [];

        foreach ($requiredDocuments as $requiredDoc) {
            $exists = self::where('user_id', $userId)
                ->where('type', $requiredDoc->document_type)
                ->where('is_profile_document', true)
                ->whereIn('status', ['pending', 'validated'])
                ->where(function ($query) use ($requiredDoc) {
                    if ($requiredDoc->has_expiry_date) {
                        $query->where(function ($q) {
                                $q->whereNull('expiry_date')
                                    ->orWhere('expiry_date', '>', now());
                            })
                            ->where('is_expired', false);
                    }
                })
                ->exists();

            if (! $exists && $requiredDoc->is_required) {
                $missingDocuments[] = $requiredDoc;
            }
        }

        return $missingDocuments;
    }

    public static function isValidDocumentType($memberType, $documentType)
    {
        return RequiredDocument::where('member_type', $memberType)
            ->where('document_type', $documentType)
            ->where('is_active', true)
            ->exists();
    }

    public static function getDocumentTypeInfo($memberType, $documentType)
    {
        return RequiredDocument::where('member_type', $memberType)
            ->where('document_type', $documentType)
            ->where('is_active', true)
            ->first();
    }

    public static function getFundingDocumentTypes()
    {
        return [
            [
                'type' => 'business_plan',
                'category' => 'project',
                'name' => 'Business Plan',
                'is_required' => true,
                'description' => 'Plan d\'affaires détaillé du projet',
                'allowed_formats' => ['pdf', 'doc', 'docx'],
                'max_size_mb' => 10
            ],
            [
                'type' => 'project_photos',
                'category' => 'project',
                'name' => 'Photos du projet',
                'is_required' => false,
                'description' => 'Photos illustrant le projet',
                'allowed_formats' => ['jpg', 'jpeg', 'png'],
                'max_size_mb' => 5
            ],
            [
                'type' => 'financial_statements',
                'category' => 'financial',
                'name' => 'États financiers',
                'is_required' => true,
                'description' => 'États financiers des 2 dernières années',
                'allowed_formats' => ['pdf', 'xls', 'xlsx'],
                'max_size_mb' => 5
            ],
            [
                'type' => 'legal_document',
                'category' => 'legal',
                'name' => 'Document légal',
                'is_required' => true,
                'description' => 'Statuts de l\'entreprise ou équivalent',
                'allowed_formats' => ['pdf', 'doc', 'docx'],
                'max_size_mb' => 5
            ]
        ];
    }

    public static function createProfileDocument($user, $data, $file)
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;

        // Chemin de stockage
        $path = 'documents/users/' . $user->id . '/profile';
        $filePath = $file->storeAs($path, $filename, 'public');

        return self::create([
            'user_id' => $user->id,
            'name' => $data['name'] ?? $data['type'],
            'type' => $data['type'],
            'category' => $data['category'] ?? 'verification',
            'is_profile_document' => true,
            'is_required' => $data['is_required'] ?? true,
            'path' => $filePath,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'original_filename' => $originalName,
            'description' => $data['description'] ?? null,
            'expiry_date' => $data['expiry_date'] ?? null,
            'status' => 'pending',
            'uploaded_at' => now()
        ]);
    }

    public static function createFundingDocument($user, $fundingRequest, $data, $file)
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;

        // Chemin de stockage
        $path = 'documents/users/' . $user->id . '/funding/' . $fundingRequest->id;
        $filePath = $file->storeAs($path, $filename, 'public');

        return self::create([
            'user_id' => $user->id,
            'funding_request_id' => $fundingRequest->id,
            'name' => $data['name'] ?? $data['type'],
            'type' => $data['type'],
            'category' => $data['category'] ?? 'project',
            'is_profile_document' => false,
            'is_required' => $data['is_required'] ?? true,
            'path' => $filePath,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'original_filename' => $originalName,
            'description' => $data['description'] ?? null,
            'status' => 'pending',
            'uploaded_at' => now()
        ]);
    }

    // Méthode pour générer un nom de fichier sécurisé
    public static function generateSafeFilename($originalName)
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $basename = pathinfo($originalName, PATHINFO_FILENAME);

        // Nettoyer le nom de base
        $cleanBasename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $basename);
        $cleanBasename = substr($cleanBasename, 0, 100); // Limiter la longueur

        return time() . '_' . $cleanBasename . '.' . strtolower($extension);
    }

    // Méthode pour vérifier la validité d'un fichier
    public static function validateFile($file, $allowedFormats = [], $maxSizeMB = 5)
    {
        $errors = [];

        // Vérifier la taille
        $maxSizeBytes = $maxSizeMB * 1024 * 1024;
        if ($file->getSize() > $maxSizeBytes) {
            $errors[] = "Le fichier dépasse la taille maximale de {$maxSizeMB} Mo";
        }

        // Vérifier l'extension
        if (!empty($allowedFormats)) {
            $extension = strtolower($file->getClientOriginalExtension());
            $mimeType = strtolower($file->getMimeType());

            $isValid = in_array($extension, $allowedFormats);

            // Vérifier aussi par type MIME pour plus de sécurité
            $mimeMap = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ];

            if (isset($mimeMap[$extension]) && $mimeMap[$extension] !== $mimeType) {
                $errors[] = "Le type MIME du fichier ne correspond pas à son extension";
            }

            if (!$isValid) {
                $errors[] = "Format non autorisé. Formats acceptés: " . implode(', ', $allowedFormats);
            }
        }

        return $errors;
    }

    // Événements
    protected static function boot()
    {
        parent::boot();

        // Avant de supprimer le document, supprimer le fichier physique
        static::deleting(function ($document) {
            $document->deleteFile();
        });

        // Après la création, vérifier l'expiration si date d'expiration
        static::created(function ($document) {
            if ($document->expiry_date) {
                $document->checkExpiry();
            }
        });

        // Après la mise à jour, vérifier l'expiration
        static::updated(function ($document) {
            if ($document->expiry_date) {
                $document->checkExpiry();
            }
        });
    }
}
