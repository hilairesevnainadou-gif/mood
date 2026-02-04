<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('training_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('trainings_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['slug', 'is_active']);
            $table->index('order');
        });
    }

    public function down()
    {
        Schema::dropIfExists('training_categories');
    }
};