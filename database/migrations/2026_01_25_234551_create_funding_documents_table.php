<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_request_id')->constrained('funding_requests')->onDelete('cascade');

            // Information du document
            $table->string('document_number')->unique(); // BHDM-DOC-YYYYMMDD-XXXX
            $table->string('title');
            $table->text('description')->nullable();

            // Type et catégorie
            $table->enum('document_type', [
                'application_form',
                'business_plan',
                'financial_statements',
                'tax_certificate',
                'identity_document',
                'proof_address',
                'bank_details',
                'technical_documents',
                'legal_documents',
                'environmental_study',
                'market_study',
                'other'
            ]);

            $table->enum('category', [
                'required',
                'optional',
                'additional',
                'supporting',
                'legal'
            ])->default('required');

            // Fichier
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('file_extension');
            $table->integer('file_size')->comment('Taille en octets');
            $table->string('mime_type');

            // Métadonnées
            $table->date('document_date')->nullable();
            $table->string('document_reference')->nullable();
            $table->string('issued_by')->nullable();
            $table->date('expiry_date')->nullable();

            // Validation
            $table->enum('validation_status', [
                'pending',
                'approved',
                'rejected',
                'requires_update',
                'expired'
            ])->default('pending');

            $table->text('validation_comments')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->timestamp('validated_at')->nullable();

            // Version
            $table->integer('version')->default(1);
            $table->boolean('is_latest')->default(true);
            $table->foreignId('previous_version_id')->nullable()->constrained('funding_documents');

            // Confidentialité
            $table->enum('confidentiality_level', [
                'public',
                'internal',
                'confidential',
                'secret'
            ])->default('internal');

            // Tags
            $table->json('tags')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('document_number');
            $table->index('document_type');
            $table->index('validation_status');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_documents');
    }
};
