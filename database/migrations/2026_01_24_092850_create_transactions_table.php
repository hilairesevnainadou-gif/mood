<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            
            // Identifiants
            $table->string('transaction_id')->unique(); // UUID
            $table->string('reference')->nullable(); // Référence externe
            
            // Informations de transaction
            $table->enum('type', ['deposit', 'withdrawal', 'transfer', 'refund', 'fee']);
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2); // amount +/- fee
            
            // Méthode de paiement
            $table->enum('payment_method', [
                'wave', 
                'orange_money', 
                'free_money', 
                'card', 
                'bank_transfer',
                'mobile_money'
            ]);
            
            // Statut et suivi
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('description')->nullable();
            
            // Métadonnées
            $table->json('metadata')->nullable(); // IP, user agent, infos additionnelles
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};