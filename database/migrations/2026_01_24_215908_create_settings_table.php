<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            
            // Clé étrangère nullable pour les paramètres globaux
            $table->foreignId('user_id')
                  ->nullable()
                  ->unique()
                  ->constrained()
                  ->onDelete('cascade');
            
            // Notifications
            $table->boolean('notification_email')->default(true);
            $table->boolean('notification_sms')->default(false);
            $table->boolean('notification_push')->default(true);
            
            // Préférences régionales
            $table->string('language', 10)->default('fr');
            $table->string('timezone', 50)->default('Africa/Abidjan');
            $table->string('date_format', 20)->default('d/m/Y');
            $table->string('currency', 10)->default('XOF');
            $table->string('theme', 20)->default('light');
            
            // Notifications détaillées
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('newsletter_subscribed')->default(true);
            
            // Sécurité
            $table->boolean('two_factor_auth')->default(false);
            $table->json('privacy_settings')->nullable();
            $table->unsignedSmallInteger('data_retention_period')->default(365);
            $table->unsignedSmallInteger('auto_logout_time')->default(30);
            
            // Interface
            $table->string('default_view', 20)->default('grid');
            $table->unsignedTinyInteger('rows_per_page')->default(25);
            
            $table->timestamps();
            
            // Index
            $table->index(['user_id', 'theme']);
            $table->index(['user_id', 'language']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};