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
       Schema::create('funding_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ex: "Subvention Agriculture"
            $table->string('code')->unique();
            $table->text('description');
            $table->decimal('amount', 15, 2); // Montant de la subvention
            $table->decimal('registration_fee', 15, 2); // Frais à payer (5000, 10000, etc.)
            $table->integer('duration_months')->default(12);
            $table->json('required_documents')->nullable(); // ["identity", "business_plan", "tax_certificate"]
            $table->string('category'); // agriculture, commerce, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funding_types');
    }
};
