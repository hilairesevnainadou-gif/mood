<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier la colonne status pour ajouter 'pending'
        DB::statement("ALTER TABLE funding_requests MODIFY status ENUM(
            'draft',
            'submitted',
            'under_review',
            'pending_committee',
            'pending',
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à l'enum original sans 'pending'
        DB::statement("ALTER TABLE funding_requests MODIFY status ENUM(
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
    }
};