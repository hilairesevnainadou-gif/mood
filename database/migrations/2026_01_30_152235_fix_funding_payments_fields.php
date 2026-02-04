<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('funding_payments', function (Blueprint $table) {
            // Rendre payment_date nullable temporairement ou avec valeur par défaut
            $table->date('payment_date')->nullable()->change();

            // OU ajouter une valeur par défaut (mais nullable est plus flexible)
            // $table->date('payment_date')->default(DB::raw('CURRENT_DATE'))->change();

            // Si payment_method est aussi requis selon votre enum
            $table->enum('payment_method', [
                'bank_transfer',
                'mobile_money',
                'cash',
                'cheque',
                'other'
            ])->default('mobile_money')->change();
        });
    }

    public function down(): void
    {
        Schema::table('funding_payments', function (Blueprint $table) {
            $table->date('payment_date')->nullable(false)->change();
            $table->enum('payment_method', [
                'bank_transfer',
                'mobile_money',
                'cash',
                'cheque',
                'other'
            ])->default(null)->change();
        });
    }
};
