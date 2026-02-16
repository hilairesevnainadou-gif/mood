<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('funding_requests', function (Blueprint $table) {
            // Champs pour le transfert différé
            $table->enum('transfer_status', ['none', 'pending', 'processing', 'completed', 'failed'])
                  ->default('none')
                  ->after('status');
            $table->timestamp('transfer_scheduled_at')->nullable()->after('transfer_status');
            $table->timestamp('transfer_completed_at')->nullable()->after('transfer_scheduled_at');
            $table->foreignId('transfer_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();

            // Champs pour la vérification des documents
            $table->boolean('documents_checked')->default(false)->after('kkiapay_amount_paid');
            $table->boolean('documents_valid')->nullable()->after('documents_checked');
            $table->json('missing_documents')->nullable()->after('documents_valid');
            $table->timestamp('documents_checked_at')->nullable()->after('missing_documents');
            $table->foreignId('documents_checked_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('funding_requests', function (Blueprint $table) {
            $table->dropForeign(['transfer_transaction_id']);
            $table->dropForeign(['documents_checked_by']);
            $table->dropColumn([
                'transfer_status',
                'transfer_scheduled_at',
                'transfer_completed_at',
                'transfer_transaction_id',
                'documents_checked',
                'documents_valid',
                'missing_documents',
                'documents_checked_at',
                'documents_checked_by',
            ]);
        });
    }
};
