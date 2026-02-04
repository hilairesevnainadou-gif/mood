<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('training_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('training_id')->constrained()->onDelete('cascade');
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status')->default('enrolled'); // enrolled, completed, cancelled, failed
            $table->integer('progress')->default(0);
            $table->string('certificate_id')->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->json('quiz_results')->nullable();
            $table->integer('time_spent_minutes')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->unique(['user_id', 'training_id']);
            $table->index(['user_id', 'status']);
            $table->index(['training_id', 'status']);
            $table->index('enrolled_at');
            $table->index('completed_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('training_user');
    }
};