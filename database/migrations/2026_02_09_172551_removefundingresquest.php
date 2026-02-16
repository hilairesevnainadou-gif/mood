<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Étape 1: Convertir temporairement en VARCHAR pour éviter la troncature
        DB::statement("ALTER TABLE funding_requests MODIFY COLUMN status VARCHAR(50) DEFAULT 'draft'");

        // Étape 2: Mettre à jour les valeurs obsolètes
        // 'pending' n'existe plus, remplacer par 'submitted'
        DB::table('funding_requests')
            ->where('status', 'pending')
            ->update(['status' => 'submitted']);

        // Étape 3: S'assurer qu'aucune valeur n'est NULL (si NOT NULL)
        DB::table('funding_requests')
            ->whereNull('status')
            ->update(['status' => 'draft']);

        // Étape 4: Recréer l'ENUM avec toutes les valeurs
        DB::statement("ALTER TABLE funding_requests MODIFY COLUMN status ENUM(
            'draft',
            'submitted',
            'under_review',
            'pending_committee',
            'validated',
            'pending_payment',
            'paid',
            'approved',
            'rejected',
            'funded',
            'in_progress',
            'completed',
            'cancelled'
        ) DEFAULT 'draft'");

        // Étape 5: Ajouter les nouvelles colonnes si elles n'existent pas
        Schema::table('funding_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('funding_requests', 'title')) {
                $table->string('title')->nullable()->after('request_number');
            }

            if (!Schema::hasColumn('funding_requests', 'is_predefined')) {
                $table->boolean('is_predefined')->default(false)->after('title');
            }

            if (!Schema::hasColumn('funding_requests', 'funding_type_id')) {
                $table->foreignId('funding_type_id')->nullable()->after('is_predefined')
                      ->constrained('funding_types')->onDelete('set null');
            }

            if (!Schema::hasColumn('funding_requests', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('funded_at');
            }

            if (!Schema::hasColumn('funding_requests', 'validated_at')) {
                $table->timestamp('validated_at')->nullable()->after('paid_at');
            }

            if (!Schema::hasColumn('funding_requests', 'validated_by')) {
                $table->foreignId('validated_by')->nullable()->after('validated_at')
                      ->constrained('users')->onDelete('set null');
            }

            if (!Schema::hasColumn('funding_requests', 'admin_validation_notes')) {
                $table->text('admin_validation_notes')->nullable()->after('validated_by');
            }

            if (!Schema::hasColumn('funding_requests', 'expected_payment')) {
                $table->decimal('expected_payment', 15, 2)->nullable()->after('admin_validation_notes');
            }

            if (!Schema::hasColumn('funding_requests', 'payment_motif')) {
                $table->string('payment_motif')->nullable()->after('expected_payment');
            }
        });

        // Étape 6: Modifier l'ENUM type pour ajouter 'custom'
        DB::statement("ALTER TABLE funding_requests MODIFY COLUMN type ENUM(
            'agriculture', 'elevage', 'peche', 'transformation',
            'artisanat', 'industrie', 'commerce', 'services',
            'tourisme', 'transport', 'technologie', 'energie_renouvelable',
            'economie_circulaire', 'custom', 'autre'
        )");

        // Étape 7: Ajouter les index pour optimisation
        Schema::table('funding_requests', function (Blueprint $table) {
            $table->index('status');
            $table->index('is_predefined');
            $table->index('funding_type_id');
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        // Restaurer l'ancien ENUM
        DB::statement("ALTER TABLE funding_requests MODIFY COLUMN status ENUM(
            'draft', 'pending', 'submitted', 'under_review',
            'pending_committee', 'approved', 'rejected',
            'funded', 'in_progress', 'completed', 'cancelled'
        ) DEFAULT 'draft'");

        Schema::table('funding_requests', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'is_predefined',
                'paid_at',
                'validated_at',
                'admin_validation_notes',
                'expected_payment',
                'payment_motif'
            ]);

            $table->dropForeign(['funding_type_id']);
            $table->dropColumn('funding_type_id');

            $table->dropForeign(['validated_by']);
            $table->dropColumn('validated_by');

            $table->dropIndex(['status']);
            $table->dropIndex(['is_predefined']);
            $table->dropIndex(['funding_type_id']);
            $table->dropIndex(['user_id', 'status']);
        });
    }
};
