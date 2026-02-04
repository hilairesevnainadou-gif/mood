<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->boolean('notification_email')->default(true);
            $table->boolean('notification_sms')->default(false);
            $table->boolean('notification_push')->default(true);
            $table->string('language')->default('fr');
            $table->string('timezone')->default('Africa/Abidjan');
            $table->string('date_format')->default('d/m/Y');
            $table->string('currency')->default('XOF');
            $table->string('theme')->default('light');
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('newsletter_subscribed')->default(true);
            $table->boolean('two_factor_auth')->default(false);
            $table->json('privacy_settings')->nullable();
            $table->integer('data_retention_period')->default(365);
            $table->integer('auto_logout_time')->default(30);
            $table->string('default_view')->default('grid');
            $table->integer('rows_per_page')->default(25);
            $table->json('custom_settings')->nullable();
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};