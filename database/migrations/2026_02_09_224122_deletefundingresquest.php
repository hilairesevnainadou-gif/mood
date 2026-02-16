<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Supprimer l'ancienne table de paiements
        Schema::dropIfExists('funding_payments');

        // Ajouter champs Kkiapay à funding_requests
        Schema::table('funding_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('funding_requests', 'kkiapay_transaction_id')) {
                $table->string('kkiapay_transaction_id')->nullable()->unique()->after('status');
            }
            if (!Schema::hasColumn('funding_requests', 'kkiapay_phone')) {
                $table->string('kkiapay_phone')->nullable()->after('kkiapay_transaction_id');
            }
            if (!Schema::hasColumn('funding_requests', 'kkiapay_amount_paid')) {
                $table->decimal('kkiapay_amount_paid', 15, 2)->nullable()->after('kkiapay_phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('funding_requests', function (Blueprint $table) {
            $table->dropColumn(['kkiapay_transaction_id', 'kkiapay_phone', 'kkiapay_amount_paid']);
        });

        // Recréer l'ancienne table (si besoin rollback)
        Schema::create('funding_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_request_id')->constrained()->onDelete('cascade');
            $table->string('payment_number')->unique();
            $table->string('payment_motif');
            $table->decimal('amount', 15, 2);
            $table->string('type')->default('registration');
            $table->string('status')->default('pending');
            $table->string('phone_number')->nullable();
            $table->string('country')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->string('payment_method')->default('kkiapay');
            $table->string('transaction_id')->nullable()->unique();
            $table->json('payment_details')->nullable();
            $table->timestamp('confirmed_by_user_at')->nullable();
            $table->timestamp('verified_by_admin_at')->nullable();
            $table->timestamps();
        });
    }
};
