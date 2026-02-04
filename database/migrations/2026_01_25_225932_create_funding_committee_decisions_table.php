<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_committee_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_request_id')->constrained('funding_requests')->onDelete('cascade');
            
            // Information du comité
            $table->enum('committee_type', [
                'local',
                'regional',
                'national',
                'technical',
                'financial',
                'strategic'
            ]);
            
            $table->string('committee_name');
            $table->string('committee_code')->nullable();
            
            // Décision
            $table->enum('decision', [
                'approved',
                'approved_with_conditions',
                'rejected',
                'deferred',
                'requires_more_info',
                'pending'
            ]);
            
            $table->date('decision_date');
            $table->decimal('approved_amount', 15, 2)->nullable();
            
            // Conditions et commentaires
            $table->text('conditions')->nullable();
            $table->text('comments')->nullable();
            $table->text('rejection_reasons')->nullable();
            
            // Membres du comité présents
            $table->json('committee_members')->nullable()->comment('Liste des membres présents');
            $table->integer('total_members')->default(0);
            $table->integer('present_members')->default(0);
            
            // Votes
            $table->integer('votes_for')->default(0);
            $table->integer('votes_against')->default(0);
            $table->integer('votes_abstention')->default(0);
            
            // Délais et recommandations
            $table->integer('funding_duration')->nullable()->comment('Durée en mois');
            $table->decimal('tps_recommended', 5, 2)->nullable()->comment('TPS recommandé (%)');
            
            // Suivi
            $table->text('next_steps')->nullable();
            $table->date('next_review_date')->nullable();
            
            // Documents
            $table->string('meeting_minutes_path')->nullable();
            $table->string('decision_file_path')->nullable();
            
            // Métadonnées
            $table->json('metadata')->nullable();
            
            // Référence
            $table->string('decision_number')->unique(); // BHDM-DEC-YYYYMMDD-XXXX
            
            $table->timestamps();
            
            // Index
            $table->index('decision_number');
            $table->index('committee_type');
            $table->index('decision');
            $table->index('decision_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_committee_decisions');
    }
};