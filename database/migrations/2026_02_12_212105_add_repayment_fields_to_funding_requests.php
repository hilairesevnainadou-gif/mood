<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ============ CORRECTION STATUS ============

        // Étape 1: Convertir temporairement en VARCHAR
        DB::statement("ALTER TABLE funding_requests MODIFY COLUMN status VARCHAR(50) DEFAULT 'draft'");

        // Étape 2: Mettre à jour les valeurs obsolètes ou invalides
        DB::table('funding_requests')
            ->whereNotIn('status', [
                'draft', 'submitted', 'under_review', 'pending_committee',
                'validated', 'pending_payment', 'paid', 'approved',
                'documents_validated', 'transfer_pending', 'funded',
                'in_progress', 'completed', 'rejected', 'cancelled'
            ])
            ->orWhereNull('status')
            ->update(['status' => 'draft']);

        // Étape 3: Recréer l'ENUM status avec TOUTES les valeurs du code
        DB::statement("ALTER TABLE funding_requests MODIFY COLUMN status ENUM(
            'draft',
            'submitted',
            'under_review',
            'pending_committee',
            'validated',
            'pending_payment',
            'paid',
            'approved',
            'documents_validated',
            'transfer_pending',
            'funded',
            'in_progress',
            'completed',
            'rejected',
            'cancelled'
        ) DEFAULT 'draft'");

        // ============ CORRECTION TRANSFER_STATUS ============

        // Étape 4: Vérifier si la colonne existe, sinon la créer
        if (!Schema::hasColumn('funding_requests', 'transfer_status')) {
            Schema::table('funding_requests', function (Blueprint $table) {
                $table->enum('transfer_status', [
                    'pending', 'scheduled', 'processing', 'completed', 'cancelled'
                ])->nullable()->after('status');
            });
        } else {
            // La colonne existe, la convertir en VARCHAR temporairement
            DB::statement("ALTER TABLE funding_requests MODIFY COLUMN transfer_status VARCHAR(50) NULL");

            // Mettre à jour les valeurs invalides
            DB::table('funding_requests')
                ->whereNotIn('transfer_status', ['pending', 'scheduled', 'processing', 'completed', 'cancelled'])
                ->whereNotNull('transfer_status')
                ->update(['transfer_status' => null]);

            // Recréer l'ENUM avec les bonnes valeurs
            DB::statement("ALTER TABLE funding_requests MODIFY COLUMN transfer_status ENUM(
                'pending',
                'scheduled',
                'processing',
                'completed',
                'cancelled'
            ) NULL");
        }

        // ============ AJOUT DES COLONNES MANQUANTES ============

        Schema::table('funding_requests', function (Blueprint $table) {
            // Colonnes de transfert
            if (!Schema::hasColumn('funding_requests', 'transfer_scheduled_at')) {
                $table->timestamp('transfer_scheduled_at')->nullable()->after('transfer_status');
            }
            if (!Schema::hasColumn('funding_requests', 'transfer_executed_at')) {
                $table->timestamp('transfer_executed_at')->nullable()->after('transfer_scheduled_at');
            }
            if (!Schema::hasColumn('funding_requests', 'transfer_cancellation_reason')) {
                $table->text('transfer_cancellation_reason')->nullable()->after('transfer_executed_at');
            }

            // Colonnes de remboursement
            if (!Schema::hasColumn('funding_requests', 'total_repayment_amount')) {
                $table->decimal('total_repayment_amount', 15, 2)->nullable()->after('amount_approved');
            }
            if (!Schema::hasColumn('funding_requests', 'monthly_repayment_amount')) {
                $table->decimal('monthly_repayment_amount', 15, 2)->nullable()->after('total_repayment_amount');
            }
            if (!Schema::hasColumn('funding_requests', 'repayment_duration_months')) {
                $table->integer('repayment_duration_months')->nullable()->after('monthly_repayment_amount');
            }
            if (!Schema::hasColumn('funding_requests', 'repayment_start_date')) {
                $table->date('repayment_start_date')->nullable()->after('repayment_duration_months');
            }
            if (!Schema::hasColumn('funding_requests', 'repayment_end_date')) {
                $table->date('repayment_end_date')->nullable()->after('repayment_start_date');
            }

            // Autres colonnes
            if (!Schema::hasColumn('funding_requests', 'documents_checked_at')) {
                $table->timestamp('documents_checked_at')->nullable()->after('repayment_end_date');
            }
            if (!Schema::hasColumn('funding_requests', 'documents_checked_by')) {
                $table->foreignId('documents_checked_by')->nullable()->after('documents_checked_at')
                      ->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('funding_requests', 'final_notes')) {
                $table->text('final_notes')->nullable()->after('documents_checked_by');
            }
        });

        // ============ INDEX ============

        Schema::table('funding_requests', function (Blueprint $table) {
            $table->index('status', 'idx_funding_status');
            $table->index('transfer_status', 'idx_funding_transfer_status');
        });
    }

    public function down(): void
    {
        // Restaurer les anciens ENUM
        DB::statement("ALTER TABLE funding_requests MODIFY COLUMN status ENUM(
            'draft', 'pending', 'submitted', 'under_review',
            'pending_committee', 'approved', 'rejected',
            'funded', 'in_progress', 'completed', 'cancelled'
        ) DEFAULT 'draft'");

        if (Schema::hasColumn('funding_requests', 'transfer_status')) {
            DB::statement("ALTER TABLE funding_requests DROP COLUMN transfer_status");
        }

        Schema::table('funding_requests', function (Blueprint $table) {
            $table->dropIndex('idx_funding_status');
            $table->dropIndex('idx_funding_transfer_status');
        });
    }
};
