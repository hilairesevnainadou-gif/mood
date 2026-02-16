<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Ajouter la colonne si elle n'existe pas
            if (!Schema::hasColumn('transactions', 'provider_transaction_id')) {
                $table->string('provider_transaction_id')->nullable()->after('reference');
                $table->index('provider_transaction_id');
            }

            // Autres colonnes potentiellement manquantes
            if (!Schema::hasColumn('transactions', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('transactions', 'metadata')) {
                $table->json('metadata')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'provider_transaction_id')) {
                $table->dropColumn('provider_transaction_id');
            }
            if (Schema::hasColumn('transactions', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
            if (Schema::hasColumn('transactions', 'metadata')) {
                $table->dropColumn('metadata');
            }
        });
    }
};
