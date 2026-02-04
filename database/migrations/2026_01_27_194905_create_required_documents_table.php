<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('required_documents', function (Blueprint $table) {
            $table->id();

            // Type de client (particulier, entreprise, etc.)
            $table->string('member_type');

            // Type de document (identity, business_plan, etc.)
            $table->string('document_type');

            // Nom affiché du document
            $table->string('name');

            // Description du document
            $table->text('description')->nullable();

            // Est-ce obligatoire ?
            $table->boolean('is_required')->default(true);

            // Catégorie du document
            $table->enum('category', [
                'personal',
                'business',
                'financial',
                'project',
                'verification',
                'other'
            ])->default('verification');

            // Ordre d'affichage
            $table->integer('order')->default(0);

            // Date d'expiration requise ?
            $table->boolean('has_expiry_date')->default(false);

            // Durée de validité en jours (si applicable)
            $table->integer('validity_days')->nullable();

            // Format de fichier acceptés
            $table->json('allowed_formats')->nullable();

            // Taille maximale en Mo
            $table->integer('max_size_mb')->default(5);

            // Active/désactive ce document
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Index pour optimiser les recherches
            $table->index(['member_type', 'document_type']);
            $table->index(['member_type', 'is_required']);
            $table->unique(['member_type', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('required_documents');
    }
};
