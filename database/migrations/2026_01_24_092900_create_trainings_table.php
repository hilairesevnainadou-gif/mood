<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();

            // Information de la formation
            $table->string('title');
            $table->text('description');
            $table->string('slug')->unique();

            // Contenu (les 5 clés)
            $table->text('content')->comment('Contenu formaté des 5 clés');

            // Métadonnées
            $table->enum('type', ['mandatory', 'optional'])->default('mandatory');
            $table->integer('duration_minutes')->default(15);
            $table->json('quiz_questions')->nullable();

            // Statut
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
