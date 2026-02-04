<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bhdm_statistics', function (Blueprint $table) {
            $table->id();

            // Données publiques
            $table->integer('total_projects_funded')->default(0);
            $table->decimal('total_amount_funded', 20, 2)->default(0);
            $table->decimal('total_amount_recycled', 20, 2)->default(0);
            $table->integer('total_jobs_created')->default(0);
            $table->integer('active_users')->default(0);

            // Par pays
            $table->json('statistics_by_country')->nullable();

            // Par secteur
            $table->json('statistics_by_sector')->nullable();

            // Données financières
            $table->decimal('initial_fund', 20, 2)->default(50000000000); // 50 milliards FCFA
            $table->decimal('current_fund', 20, 2)->default(50000000000);
            $table->decimal('repayment_rate', 5, 2)->default(0);

            // Métadonnées
            $table->date('statistics_date');
            $table->boolean('is_published')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bhdm_statistics');
    }
};
