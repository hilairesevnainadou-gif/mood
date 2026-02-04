<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('funding_payments', function (Blueprint $table) {
            // Champ clé : Motif à 4 chiffres (ex: 7392)
            $table->string('payment_motif', 4)->nullable()->after('payment_number')
                ->comment('Code à 4 chiffres à mentionner lors du paiement');

            // Type de paiement (inscription vs frais de transfert)
            $table->enum('type', ['registration', 'transfer_fee', 'additional'])
                ->default('registration')
                ->after('payment_method')
                ->comment('registration=frais inscription, transfer_fee=frais urgent, etc.');

            // Pour les demandes personnalisées validées
            $table->decimal('approved_amount', 15, 2)->nullable()->after('amount')
                ->comment('Montant de la subvention approuvée (pour les demandes custom)');

            // Validation par l'admin
            $table->foreignId('verified_by')->nullable()->after('created_by')
                ->constrained('users')
                ->comment('Admin qui a vérifié le paiement');

            $table->timestamp('verified_at')->nullable()->after('verified_by')
                ->comment('Date de validation admin');

            // Confirmation par le client
            $table->timestamp('confirmed_by_user_at')->nullable()->after('verified_at')
                ->comment('Date où le client a signalé avoir payé');

            // Pays pour filtrer les opérateurs
            $table->string('country', 50)->nullable()->after('phone_number')
                ->comment('Pays du paiement pour statistics');

            // Notes spécifiques admin (séparé de comments général)
            $table->text('admin_notes')->nullable()->after('comments')
                ->comment('Notes de validation/rejet par l\'admin');

            // Index pour performances
            $table->index('payment_motif');
            $table->index('type');
            $table->index('country');
            $table->index(['status', 'type']);
        });
    }

    public function down(): void
    {
        Schema::table('funding_payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_motif',
                'type',
                'approved_amount',
                'verified_by',
                'verified_at',
                'confirmed_by_user_at',
                'country',
                'admin_notes'
            ]);
        });
    }
};
