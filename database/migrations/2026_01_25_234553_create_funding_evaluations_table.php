<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_request_id')->constrained('funding_requests')->onDelete('cascade');
            
            // Information de l'évaluation
            $table->string('evaluation_number')->unique(); // BHDM-EVAL-YYYYMMDD-XXXX
            $table->enum('evaluation_type', [
                'initial',
                'technical',
                'financial',
                'environmental',
                'social',
                'risk',
                'final',
                'mid_term',
                'end_term',
                'special'
            ]);
            
            $table->string('title');
            $table->text('purpose')->nullable();
            
            // Évaluateur
            $table->foreignId('evaluator_id')->constrained('users');
            $table->foreignId('reviewer_id')->nullable()->constrained('users');
            
            // Dates
            $table->date('evaluation_date');
            $table->date('planned_date');
            $table->date('completed_date')->nullable();
            
            // Statut
            $table->enum('status', [
                'planned',
                'in_progress',
                'completed',
                'cancelled',
                'pending_review',
                'approved',
                'rejected'
            ])->default('planned');
            
            // Scores et critères
            $table->json('criteria_scores')->nullable()->comment('Scores par critère');
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->decimal('technical_score', 5, 2)->nullable();
            $table->decimal('financial_score', 5, 2)->nullable();
            $table->decimal('social_score', 5, 2)->nullable();
            $table->decimal('environmental_score', 5, 2)->nullable();
            
            // Recommandation
            $table->enum('recommendation', [
                'approve',
                'approve_with_conditions',
                'reject',
                'defer',
                'require_more_info',
                'modify'
            ])->nullable();
            
            $table->text('recommendation_details')->nullable();
            
            // Évaluation détaillée
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('opportunities')->nullable();
            $table->text('threats')->nullable();
            
            $table->text('financial_analysis')->nullable();
            $table->text('market_analysis')->nullable();
            $table->text('technical_analysis')->nullable();
            $table->text('risk_analysis')->nullable();
            
            // Conditions et exigences
            $table->text('conditions')->nullable();
            $table->text('requirements')->nullable();
            $table->text('suggestions')->nullable();
            
            // Documents liés
            $table->string('report_file_path')->nullable();
            $table->string('attachments_path')->nullable();
            
            // Validation
            $table->boolean('is_validated')->default(false);
            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->timestamp('validated_at')->nullable();
            $table->text('validation_comments')->nullable();
            
            // Métadonnées
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index('evaluation_number');
            $table->index('evaluation_type');
            $table->index('status');
            $table->index('evaluator_id');
            $table->index('evaluation_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_evaluations');
    }
};