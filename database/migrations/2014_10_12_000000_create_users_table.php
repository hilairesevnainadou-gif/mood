<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Informations personnelles
            $table->string('first_name');
            $table->string('last_name');
            $table->string('name'); // Nom complet (first_name + last_name)
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('profile_photo')->nullable();

            // Démographie
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            // Localisation
            $table->string('address')->nullable();
            $table->string('city');
            $table->enum('country', [
                'senegal',
                'cote_ivoire',
                'mali',
                'burkina_faso',
                'guinee',
                'benin',
                'togo',
                'niger',
                'mauritanie',
                'autre_afrique_ouest'
            ]);
            $table->string('postal_code')->nullable();

            // Informations entreprise (si applicable)
            $table->string('company_name')->nullable();
            $table->enum('company_type', ['sarl', 'sa', 'snc', 'ei', 'cooperative', 'ong', 'autre'])->nullable();
            $table->enum('sector', [
                'agriculture',
                'elevage',
                'peche',
                'industrie',
                'commerce',
                'services',
                'tourisme',
                'batiment',
                'technologie',
                'autre'
            ])->nullable();
            $table->string('job_title')->nullable();
            $table->integer('employees_count')->default(0);
            $table->decimal('annual_turnover', 15, 2)->default(0);

            // Identifiants et statuts
            $table->string('member_id')->unique()->nullable(); // BHDM-2024-000001
            $table->date('member_since')->nullable();
            $table->enum('member_status', ['pending', 'active', 'suspended', 'inactive'])->default('pending');
            $table->enum('member_type', ['particulier', 'entreprise'])->default('particulier');

            // Statuts système
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_moderator')->default(false);

            // Préférences
            $table->boolean('accepts_newsletter')->default(true);
            $table->boolean('accepts_notifications')->default(true);

            // Historique de connexion
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->text('last_login_device')->nullable();

            // Données additionnelles
            $table->json('settings')->nullable(); // Préférences utilisateur
            $table->json('preferences')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
