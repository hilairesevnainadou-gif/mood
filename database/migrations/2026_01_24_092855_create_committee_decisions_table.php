<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('committee_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('local_committee_id')->constrained()->onDelete('cascade');
            
            // Décision
            $table->enum('decision', ['approved', 'rejected', 'pending_modification']);
            $table->decimal('amount_approved', 15, 2)->nullable();
            $table->decimal('tps_final', 5, 2)->nullable();
            
            // Conditions et commentaires
            $table->text('comments')->nullable();
            $table->json('conditions')->nullable();
            $table->text('modification_requests')->nullable();
            
            // Vote
            $table->integer('votes_for')->default(0);
            $table->integer('votes_against')->default(0);
            $table->integer('votes_abstention')->default(0);
            
            // Métadonnées
            $table->timestamp('review_started_at')->nullable();
            $table->timestamp('decision_date')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('committee_decisions');
    }
};