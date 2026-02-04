<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundingDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'funding_request_id',
        'user_id', // Ajouter user_id pour relation directe
        'document_number',
        'title',
        'description',
        'document_type',
        'category',
        'file_path',
        'original_filename',
        'file_extension',
        'file_size',
        'mime_type',
        'document_date',
        'document_reference',
        'issued_by',
        'expiry_date',
        'validation_status',
        'validation_comments',
        'validated_by',
        'validated_at',
        'version',
        'is_latest',
        'previous_version_id',
        'confidentiality_level',
        'tags',
        'is_required_for_request'
    ];

    protected $casts = [
        'document_date' => 'date',
        'expiry_date' => 'date',
        'validated_at' => 'datetime',
        'file_size' => 'integer',
        'version' => 'integer',
        'is_latest' => 'boolean',
        'is_required_for_request' => 'boolean',
        'tags' => 'array'
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

    // Relations
    public function funding()
    {
        return $this->belongsTo(Funding::class, 'funding_request_id');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function previousVersion()
    {
        return $this->belongsTo(FundingDocument::class, 'previous_version_id');
    }

    public function nextVersions()
    {
        return $this->hasMany(FundingDocument::class, 'previous_version_id');
    }

    // Accessors
    public function getDocumentTypeLabelAttribute()
    {
        $labels = [
            'application_form' => 'Formulaire de demande',
            'business_plan' => 'Plan d\'affaires',
            'financial_statements' => 'États financiers',
            'tax_certificate' => 'Certificat fiscal',
            'identity_document' => 'Pièce d\'identité',
            'proof_address' => 'Justificatif de domicile',
            'bank_details' => 'Coordonnées bancaires',
            'technical_documents' => 'Documents techniques',
            'legal_documents' => 'Documents légaux',
            'environmental_study' => 'Étude environnementale',
            'market_study' => 'Étude de marché',
            'other' => 'Autre'
        ];

        return $labels[$this->document_type] ?? $this->document_type;
    }

    public function getCategoryLabelAttribute()
    {
        $labels = [
            'required' => 'Obligatoire',
            'optional' => 'Optionnel',
            'additional' => 'Additionnel',
            'supporting' => 'Support',
            'legal' => 'Légal'
        ];

        return $labels[$this->category] ?? $this->category;
    }

    public function getValidationStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            'requires_update' => 'Nécessite mise à jour',
            'expired' => 'Expiré'
        ];

        return $labels[$this->validation_status] ?? $this->validation_status;
    }

    public function getConfidentialityLevelLabelAttribute()
    {
        $labels = [
            'public' => 'Public',
            'internal' => 'Interne',
            'confidential' => 'Confidentiel',
            'secret' => 'Secret'
        ];

        return $labels[$this->confidentiality_level] ?? $this->confidentiality_level;
    }

    public function getFileSizeFormattedAttribute()
    {
        $units = ['o', 'Ko', 'Mo', 'Go'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    public function getIsValidAttribute()
    {
        return $this->validation_status === 'approved';
    }

    public function getIsExpiredAttribute()
    {
        if (!$this->expiry_date) {
            return false;
        }

        return now()->gt($this->expiry_date);
    }

    public function getIsPendingAttribute()
    {
        return $this->validation_status === 'pending';
    }

    public function getRequiresUpdateAttribute()
    {
        return $this->validation_status === 'requires_update';
    }

    // Méthodes d'action
    public function approve($comments = null, $userId = null)
    {
        $this->update([
            'validation_status' => 'approved',
            'validation_comments' => $comments,
            'validated_by' => $userId ?? auth()->id(),
            'validated_at' => now()
        ]);
    }

    public function reject($comments, $userId = null)
    {
        $this->update([
            'validation_status' => 'rejected',
            'validation_comments' => $comments,
            'validated_by' => $userId ?? auth()->id(),
            'validated_at' => now()
        ]);
    }

    public function markAsRequiresUpdate($comments, $userId = null)
    {
        $this->update([
            'validation_status' => 'requires_update',
            'validation_comments' => $comments,
            'validated_by' => $userId ?? auth()->id(),
            'validated_at' => now()
        ]);
    }

    public function createNewVersion($fileData)
    {
        // Marquer la version actuelle comme non-latest
        $this->update(['is_latest' => false]);

        // Créer la nouvelle version
        $newVersion = $this->replicate();
        $newVersion->fill(array_merge($fileData, [
            'previous_version_id' => $this->id,
            'version' => $this->version + 1,
            'is_latest' => true,
            'validation_status' => 'pending',
            'validation_comments' => null,
            'validated_by' => null,
            'validated_at' => null
        ]));

        $newVersion->save();

        return $newVersion;
    }

    // Génération du numéro de document
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            if (!$document->document_number) {
                $document->document_number = static::generateDocumentNumber();
            }
        });
    }

    public static function generateDocumentNumber()
    {
        $prefix = 'BHDM-DOC-';
        $datePart = date('Ymd');
        $lastDocument = static::where('document_number', 'like', $prefix . $datePart . '-%')
            ->orderBy('document_number', 'desc')
            ->first();

        if ($lastDocument) {
            $lastNumber = intval(substr($lastDocument->document_number, -4));
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $datePart . '-' . $nextNumber;
    }
}
