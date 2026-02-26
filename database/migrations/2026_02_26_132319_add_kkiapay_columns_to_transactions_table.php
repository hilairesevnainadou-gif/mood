<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Colonne pour l'ID de transaction Kkiapay
            $table->string('kkiapay_transaction_id')->nullable()->after('reference')->index();

            // Réponse complète de Kkiapay (stockée en JSON)
            $table->json('kkiapay_response')->nullable()->after('metadata');

            // Raison d'échec si applicable
            $table->string('failure_reason')->nullable()->after('status');

            // Date de paiement/paiement échoué
            $table->timestamp('paid_at')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'kkiapay_transaction_id',
                'kkiapay_response',
                'failure_reason',
                'paid_at'
            ]);
        });
    }
};
