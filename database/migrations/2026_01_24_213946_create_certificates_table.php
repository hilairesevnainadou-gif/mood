<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_id')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('training_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('certificate_number')->unique();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->string('certificate_url')->nullable();
            $table->string('template')->default('default');
            $table->boolean('is_verified')->default(true);
            $table->string('verification_code')->nullable()->unique();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index('certificate_number');
            $table->index(['user_id', 'training_id']);
            $table->index('issue_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('certificates');
    }
};