<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Informations du remboursement
            $table->string('repayment_number')->unique(); // BHDM-REP-YYYYMM-XXXX
            $table->decimal('amount', 15, 2);
            $table->decimal('tps_amount', 15, 2)->comment('Part solidaire');
            $table->decimal('capital_amount', 15, 2)->comment('Part capital');
            
            // Échéance
            $table->date('due_date');
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            
            // Paiement
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('transaction_reference')->nullable();
            
            // Historique
            $table->integer('installment_number')->comment('Numéro de l\'échéance');
            $table->integer('total_installments')->comment('Nombre total d\'échéances');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repayments');
    }
};