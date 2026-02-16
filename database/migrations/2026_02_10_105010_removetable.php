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
        // Modifier la colonne payment_motif pour accepter plus de caractères
        Schema::table('funding_requests', function (Blueprint $table) {
            $table->string('payment_motif', 500)->nullable()->change();
        });

        // Corriger les données existantes qui ont des nombres dans payment_motif
        DB::table('funding_requests')
            ->whereRaw('payment_motif REGEXP "^[0-9]+$"')
            ->orWhereRaw('LENGTH(payment_motif) <= 5')
            ->update([
                'payment_motif' => DB::raw("CONCAT('Frais d\'adhésion au programme - Réf: ', request_number)")
            ]);

        // Mettre à NULL les valeurs vides ou numériques restantes
        DB::table('funding_requests')
            ->whereRaw('payment_motif REGEXP "^[0-9]+$"')
            ->update(['payment_motif' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funding_requests', function (Blueprint $table) {
            $table->string('payment_motif', 255)->nullable()->change();
        });
    }
};
