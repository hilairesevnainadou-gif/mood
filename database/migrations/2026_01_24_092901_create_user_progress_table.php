<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Mission 1 - Formation
            $table->foreignId('training_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('mission_1_completed')->default(false);
            $table->timestamp('mission_1_completed_at')->nullable();

            // Mission 2 - Documents
            $table->json('required_documents')->nullable();
            $table->boolean('mission_2_completed')->default(false);
            $table->timestamp('mission_2_completed_at')->nullable();

            // Évaluation globale
            $table->enum('current_phase', [
                'registration',
                'mission_1',
                'mission_2',
                'submission',
                'committee_review',
                'approved',
                'funded',
                'in_progress'
            ])->default('registration');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};
