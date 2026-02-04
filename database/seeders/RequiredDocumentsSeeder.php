<?php

namespace Database\Seeders;

use App\Models\RequiredDocument;
use Illuminate\Database\Seeder;

class RequiredDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        // Documents pour les particuliers
        $particulierDocuments = [
            [
                'member_type' => 'particulier',
                'document_type' => 'identity',
                'name' => 'Carte nationale d\'identité ou Passeport',
                'description' => 'Pièce d\'identité officielle en cours de validité',
                'is_required' => true,
                'category' => 'verification',
                'order' => 1,
                'has_expiry_date' => true,
                'validity_days' => null,
                'allowed_formats' => ['pdf', 'jpg', 'jpeg', 'png'],
                'max_size_mb' => 5
            ],
            [
                'member_type' => 'particulier',
                'document_type' => 'proof_address',
                'name' => 'Justificatif de domicile',
                'description' => 'Facture d\'électricité, eau, téléphone ou quittance de loyer de moins de 3 mois',
                'is_required' => true,
                'category' => 'verification',
                'order' => 2,
                'has_expiry_date' => true,
                'validity_days' => 90,
                'allowed_formats' => ['pdf', 'jpg', 'jpeg', 'png'],
                'max_size_mb' => 5
            ],
            [
                'member_type' => 'particulier',
                'document_type' => 'tax_document',
                'name' => 'Avis d\'imposition',
                'description' => 'Dernier avis d\'imposition',
                'is_required' => true,
                'category' => 'financial',
                'order' => 3,
                'has_expiry_date' => true,
                'validity_days' => 365,
                'allowed_formats' => ['pdf'],
                'max_size_mb' => 10
            ],
            [
                'member_type' => 'particulier',
                'document_type' => 'project_photos',
                'name' => 'Photos du projet',
                'description' => 'Photos illustrant le projet (minimum 3 photos)',
                'is_required' => false,
                'category' => 'project',
                'order' => 4,
                'has_expiry_date' => false,
                'allowed_formats' => ['jpg', 'jpeg', 'png'],
                'max_size_mb' => 15
            ]
        ];

        // Documents pour les entreprises
        $entrepriseDocuments = [
            [
                'member_type' => 'entreprise',
                'document_type' => 'identity',
                'name' => 'Pièce d\'identité du représentant légal',
                'description' => 'Carte nationale d\'identité ou Passeport du dirigeant',
                'is_required' => true,
                'category' => 'verification',
                'order' => 1,
                'has_expiry_date' => true,
                'validity_days' => null,
                'allowed_formats' => ['pdf', 'jpg', 'jpeg', 'png'],
                'max_size_mb' => 5
            ],
            [
                'member_type' => 'entreprise',
                'document_type' => 'legal_document',
                'name' => 'Extrait Kbis ou RCS',
                'description' => 'Extrait datant de moins de 3 mois',
                'is_required' => true,
                'category' => 'business',
                'order' => 2,
                'has_expiry_date' => true,
                'validity_days' => 90,
                'allowed_formats' => ['pdf'],
                'max_size_mb' => 5
            ],
            [
                'member_type' => 'entreprise',
                'document_type' => 'financial_statements',
                'name' => 'Bilans comptables des 3 dernières années',
                'description' => 'Comptes annuels certifiés',
                'is_required' => true,
                'category' => 'financial',
                'order' => 3,
                'has_expiry_date' => false,
                'allowed_formats' => ['pdf', 'xls', 'xlsx'],
                'max_size_mb' => 20
            ],
            [
                'member_type' => 'entreprise',
                'document_type' => 'tax_document',
                'name' => 'Dernier avis d\'imposition',
                'description' => 'Avis d\'imposition de l\'entreprise',
                'is_required' => true,
                'category' => 'financial',
                'order' => 4,
                'has_expiry_date' => true,
                'validity_days' => 365,
                'allowed_formats' => ['pdf'],
                'max_size_mb' => 10
            ],
            [
                'member_type' => 'entreprise',
                'document_type' => 'business_plan',
                'name' => 'Business Plan',
                'description' => 'Business Plan détaillé du projet',
                'is_required' => true,
                'category' => 'project',
                'order' => 5,
                'has_expiry_date' => false,
                'allowed_formats' => ['pdf', 'doc', 'docx'],
                'max_size_mb' => 20
            ],
            [
                'member_type' => 'entreprise',
                'document_type' => 'project_photos',
                'name' => 'Photos du projet/activité',
                'description' => 'Photos illustrant le projet ou l\'activité de l\'entreprise',
                'is_required' => false,
                'category' => 'project',
                'order' => 6,
                'has_expiry_date' => false,
                'allowed_formats' => ['jpg', 'jpeg', 'png'],
                'max_size_mb' => 15
            ]
        ];

        // Insérer les données
        foreach ($particulierDocuments as $document) {
            RequiredDocument::create($document);
        }

        foreach ($entrepriseDocuments as $document) {
            RequiredDocument::create($document);
        }
    }
}
