<?php
// database/migrations/xxxx_xx_xx_add_transfer_fields_to_funding_requests.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('funding_requests', function (Blueprint $table) {

            // ================= TRANSFERT =================

            if (!Schema::hasColumn('funding_requests', 'documents_checked_at')) {
                $table->timestamp('documents_checked_at')
                      ->nullable()
                      ->after('validated_at');
            }

            if (!Schema::hasColumn('funding_requests', 'documents_checked_by')) {
                $table->unsignedBigInteger('documents_checked_by')
                      ->nullable()
                      ->after('documents_checked_at');
            }

            if (!Schema::hasColumn('funding_requests', 'transfer_scheduled_at')) {
                $table->timestamp('transfer_scheduled_at')
                      ->nullable()
                      ->after('documents_checked_by');
            }

            if (!Schema::hasColumn('funding_requests', 'transfer_executed_at')) {
                $table->timestamp('transfer_executed_at')
                      ->nullable()
                      ->after('transfer_scheduled_at');
            }

            if (!Schema::hasColumn('funding_requests', 'transfer_status')) {
                $table->enum('transfer_status', ['pending', 'scheduled', 'completed', 'cancelled'])
                      ->default('pending')
                      ->after('transfer_executed_at');
            }

            // ================= REMBOURSEMENT =================

            if (!Schema::hasColumn('funding_requests', 'total_repayment_amount')) {
                $table->decimal('total_repayment_amount', 15, 2)
                      ->nullable()
                      ->after('amount_approved');
            }

            if (!Schema::hasColumn('funding_requests', 'monthly_repayment_amount')) {
                $table->decimal('monthly_repayment_amount', 15, 2)
                      ->nullable()
                      ->after('total_repayment_amount');
            }

            if (!Schema::hasColumn('funding_requests', 'repayment_duration_months')) {
                $table->integer('repayment_duration_months')
                      ->nullable()
                      ->after('monthly_repayment_amount');
            }

            if (!Schema::hasColumn('funding_requests', 'repayment_start_date')) {
                $table->date('repayment_start_date')
                      ->nullable()
                      ->after('repayment_duration_months');
            }

            if (!Schema::hasColumn('funding_requests', 'repayment_end_date')) {
                $table->date('repayment_end_date')
                      ->nullable()
                      ->after('repayment_start_date');
            }

            if (!Schema::hasColumn('funding_requests', 'credited_at')) {
                $table->timestamp('credited_at')
                      ->nullable()
                      ->after('repayment_end_date');
            }

            // ================= NOTES =================

            if (!Schema::hasColumn('funding_requests', 'transfer_cancellation_reason')) {
                $table->text('transfer_cancellation_reason')
                      ->nullable()
                      ->after('transfer_status');
            }

            if (!Schema::hasColumn('funding_requests', 'final_notes')) {
                $table->text('final_notes')
                      ->nullable()
                      ->after('transfer_cancellation_reason');
            }

        });
    }

    public function down(): void
    {
        Schema::table('funding_requests', function (Blueprint $table) {

            $columns = [
                'documents_checked_at',
                'documents_checked_by',
                'transfer_scheduled_at',
                'transfer_executed_at',
                'transfer_status',
                'total_repayment_amount',
                'monthly_repayment_amount',
                'repayment_duration_months',
                'repayment_start_date',
                'repayment_end_date',
                'credited_at',
                'transfer_cancellation_reason',
                'final_notes'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('funding_requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
