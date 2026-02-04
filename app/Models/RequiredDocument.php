<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequiredDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_type',
        'document_type',
        'name',
        'description',
        'is_required',
        'category',
        'order',
        'has_expiry_date',
        'validity_days',
        'allowed_formats',
        'max_size_mb',
        'is_active'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'has_expiry_date' => 'boolean',
        'is_active' => 'boolean',
        'allowed_formats' => 'array',
        'order' => 'integer',
        'max_size_mb' => 'integer',
        'validity_days' => 'integer'
    ];

    // Constantes pour les types de membres
    const MEMBER_TYPE_PARTICULIER = 'particulier';
    const MEMBER_TYPE_ENTREPRISE = 'entreprise';

    // Constantes pour les types de documents
    const TYPE_IDENTITY = 'identity';
    const TYPE_BUSINESS_PLAN = 'business_plan';
    const TYPE_FINANCIAL_STATEMENTS = 'financial_statements';
    const TYPE_TAX_DOCUMENT = 'tax_document';
    const TYPE_LEGAL_DOCUMENT = 'legal_document';
    const TYPE_PROJECT_PHOTOS = 'project_photos';
    const TYPE_PROOF_ADDRESS = 'proof_address';
    const TYPE_OTHER = 'other';

    // Méthode pour récupérer les documents requis par type de membre
    public static function getByMemberType($memberType, $onlyRequired = true)
    {
        $query = self::where('member_type', $memberType)
            ->where('is_active', true)
            ->orderBy('order', 'asc')
            ->orderBy('name', 'asc');

        if ($onlyRequired) {
            $query->where('is_required', true);
        }

        return $query->get();
    }

    // Méthode pour vérifier si un document est requis pour un type de membre
    public static function isRequired($memberType, $documentType)
    {
        return self::where('member_type', $memberType)
            ->where('document_type', $documentType)
            ->where('is_required', true)
            ->where('is_active', true)
            ->exists();
    }

    // Méthode pour obtenir les types de membres disponibles
    public static function getMemberTypes()
    {
        return [
            self::MEMBER_TYPE_PARTICULIER => 'Particulier',
            self::MEMBER_TYPE_ENTREPRISE => 'Entreprise'
        ];
    }

    // Méthode pour obtenir les types de documents disponibles
    public static function getDocumentTypes()
    {
        return [
            self::TYPE_IDENTITY => 'Pièce d\'identité',
            self::TYPE_BUSINESS_PLAN => 'Business Plan',
            self::TYPE_FINANCIAL_STATEMENTS => 'États financiers',
            self::TYPE_TAX_DOCUMENT => 'Document fiscal',
            self::TYPE_LEGAL_DOCUMENT => 'Document juridique',
            self::TYPE_PROJECT_PHOTOS => 'Photos du projet',
            self::TYPE_PROOF_ADDRESS => 'Justificatif de domicile',
            self::TYPE_OTHER => 'Autre document'
        ];
    }

    // Accessor pour le nom complet du type de document
    public function getDocumentTypeNameAttribute()
    {
        $types = self::getDocumentTypes();
        return $types[$this->document_type] ?? $this->document_type;
    }

    // Accessor pour le nom complet du type de membre
    public function getMemberTypeNameAttribute()
    {
        $types = self::getMemberTypes();
        return $types[$this->member_type] ?? $this->member_type;
    }

    // Scope pour les documents actifs
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope pour les documents requis
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    // Scope par type de membre
    public function scopeByMemberType($query, $memberType)
    {
        return $query->where('member_type', $memberType);
    }
}
