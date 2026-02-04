<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_request_id')->constrained('funding_requests')->onDelete('cascade');
            
            // Informations de paiement
            $table->string('payment_number')->unique(); // BHDM-PAY-YYYYMMDD-XXXX
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            
            // Méthode et statut
            $table->enum('payment_method', [
                'bank_transfer',
                'mobile_money',
                'cash',
                'cheque',
                'other'
            ]);
            
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'cancelled',
                'refunded'
            ])->default('pending');
            
            // Références et métadonnées
            $table->string('reference')->nullable()->comment('Référence du paiement');
            $table->string('transaction_id')->nullable()->comment('ID de transaction externe');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('mobile_operator')->nullable();
            $table->string('phone_number')->nullable();
            
            // Suivi
            $table->text('comments')->nullable();
            $table->json('metadata')->nullable();
            
            // Dates
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            
            // Créé par
            $table->foreignId('created_by')->nullable()->constrained('users');
            
            $table->timestamps();
        });
        
        // Index pour optimisation
        Schema::table('funding_payments', function (Blueprint $table) {
            $table->index('payment_number');
            $table->index('status');
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_payments');
    }
};