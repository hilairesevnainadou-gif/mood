<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('local_committees', function (Blueprint $table) {
            $table->id();

            // Identité du comité
            $table->string('name');
            $table->enum('country', [
                'senegal',
                'cote_ivoire',
                'mali',
                'burkina_faso',
                'guinee',
                'benin',
                'togo',
                'niger',
                'mauritanie'
            ])->unique();

            // Composition
            $table->json('members')->comment('Liste des membres avec noms et titres');

            // Contact
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->string('address')->nullable();
            $table->string('city')->nullable();

            // Statut
            $table->boolean('is_active')->default(true);

            // Statistiques
            $table->integer('projects_reviewed')->default(0);
            $table->integer('projects_approved')->default(0);
            $table->decimal('total_amount_approved', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('local_committees');
    }
};
