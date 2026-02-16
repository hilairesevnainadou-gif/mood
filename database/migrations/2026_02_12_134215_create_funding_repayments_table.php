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
        Schema::create('funding_repayments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('funding_request_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('repayment_number')->unique();

            $table->date('due_date');

            $table->decimal('amount_due', 15, 2);
            $table->decimal('amount_paid', 15, 2)->nullable();

            $table->string('status')->default('pending');

            $table->timestamp('paid_at')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payment_method')->nullable();

            $table->decimal('late_fees', 15, 2)->default(0);

            $table->text('notes')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funding_repayments');
    }
};
