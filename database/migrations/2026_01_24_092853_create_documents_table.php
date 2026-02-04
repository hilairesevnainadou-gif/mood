<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('funding_request_id')->nullable()->constrained()->onDelete('cascade');

            // Information du document
            $table->string('name');
            $table->enum('type', [
                'identity',
                'business_plan',
                'financial_statements',
                'tax_document',
                'legal_document',
                'project_photos',
                'proof_address',
                'other',
            ]);

            // AJOUTER : Catégorie pour distinguer les documents
            $table->enum('category', [
                'personal',      // Documents personnels (particuliers)
                'business',      // Documents d'entreprise
                'financial',     // Documents financiers
                'project',       // Documents de projet
                'verification',  // Documents de vérification
                'other',
            ])->default('verification');

            // AJOUTER : Pour les documents de profil uniquement
            $table->boolean('is_profile_document')->default(false);
            $table->boolean('is_required')->default(false);
            $table->date('expiry_date')->nullable();
            $table->boolean('is_expired')->default(false);

            // Fichier
            $table->string('path');
            $table->string('mime_type');
            $table->bigInteger('size');
            $table->string('original_filename');

            // Description
            $table->text('description')->nullable();

            // Validation
            $table->enum('status', ['pending', 'validated', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users');

            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['user_id', 'is_profile_document']);
            $table->index(['user_id', 'category']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
