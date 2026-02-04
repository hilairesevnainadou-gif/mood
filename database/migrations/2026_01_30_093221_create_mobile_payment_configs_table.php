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
       Schema::create('mobile_payment_configs', function (Blueprint $table) {
            $table->id();
            $table->string('country'); // 'senegal', 'cote_ivoire', 'benin', etc.
            $table->string('operator_name'); // 'Orange Money', 'Wave', 'MTN MoMo'
            $table->string('operator_code'); // 'orange', 'wave', 'mtn', 'moov'
            $table->string('merchant_code'); // Code marchand chez l'opérateur
            $table->string('ussd_pattern'); // '*880*41*{merchant_code}*{amount}*{motif}#'
            $table->text('payment_instructions');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['country', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_payment_configs');
    }
};
