<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_request_id')->constrained('funding_requests')->onDelete('cascade');
            
            // Identification de la mission
            $table->enum('mission_type', [
                'mission_1',
                'mission_2',
                'follow_up',
                'audit',
                'other'
            ]);
            
            $table->string('mission_number')->unique(); // BHDM-MIS-YYYYMMDD-XXXX
            
            // Description
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('objectives')->nullable();
            
            // Attribution
            $table->foreignId('assigned_to')->constrained('users');
            $table->foreignId('supervised_by')->nullable()->constrained('users');
            
            // Dates
            $table->date('planned_start_date');
            $table->date('planned_end_date');
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();
            
            // Statut et progression
            $table->enum('status', [
                'planned',
                'in_progress',
                'completed',
                'cancelled',
                'delayed'
            ])->default('planned');
            
            $table->integer('progress_percentage')->default(0);
            
            // Résultats
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('challenges')->nullable();
            $table->text('solutions_proposed')->nullable();
            
            // Évaluation
            $table->decimal('evaluation_score', 5, 2)->nullable();
            $table->text('evaluation_comments')->nullable();
            
            // Budget de la mission
            $table->decimal('mission_budget', 15, 2)->nullable();
            $table->decimal('actual_expenses', 15, 2)->nullable();
            
            // Documents
            $table->string('report_file_path')->nullable();
            $table->string('photos_path')->nullable();
            
            // Localisation
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index('mission_number');
            $table->index('status');
            $table->index('mission_type');
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_missions');
    }
};