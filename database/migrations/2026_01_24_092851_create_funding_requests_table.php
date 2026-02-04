<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Identifiants
            $table->string('request_number')->unique(); // BHDM-REQ-YYYYMMDD-XXXX
            $table->string('title');

            // Description du projet (selon les 5 clés)
            $table->enum('type', [
                'agriculture',
                'elevage',
                'peche',
                'transformation',
                'artisanat',
                'industrie',
                'commerce',
                'services',
                'tourisme',
                'transport',
                'technologie',
                'energie_renouvelable',
                'economie_circulaire',
                'autre'
            ]);

            // Financement
            $table->decimal('amount_requested', 15, 2);
            $table->decimal('amount_approved', 15, 2)->nullable();
            $table->integer('duration')->comment('Durée en mois');

            // Description (selon les 5 clés)
            $table->text('description');
            $table->string('project_location');
            $table->integer('expected_jobs')->default(0);

            // Évaluation et TPS (Taux de Participation Solidaire)
            $table->decimal('tps_estimated', 5, 2)->nullable(); // % estimé
            $table->decimal('tps_final', 5, 2)->nullable(); // % final validé

            // Statuts
            $table->enum('status', [
                'draft',
                'pending',
                'submitted',
                'under_review',
                'pending_committee',
                'approved',
                'rejected',
                'funded',
                'in_progress',
                'completed',
                'cancelled'
            ])->default('draft');

            // Évaluation
            $table->enum('evaluation_status', [
                'mission_1_pending',
                'mission_1_completed',
                'mission_2_pending',
                'mission_2_completed',
                'under_local_committee_review',
                'committee_decision_pending'
            ])->nullable();

            // Dates importantes
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('committee_review_started_at')->nullable();
            $table->timestamp('committee_decision_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('funded_at')->nullable();

            // Relation avec comité local
            $table->string('local_committee_country')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_requests');
    }
};
