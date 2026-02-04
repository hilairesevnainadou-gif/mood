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
        Schema::table('funding_requests', function (Blueprint $table) {
        $table->string('project_location')->nullable()->change();
        $table->integer('expected_jobs')->nullable()->default(0)->change();
        // Si vous voulez permettre 'predefined'/'custom' dans type, changez en string:
        // $table->string('type', 50)->change();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funding_requests', function (Blueprint $table) {
            $table->string('project_location')->nullable(false)->change();
            $table->integer('expected_jobs')->nullable(false)->default(0)->change();
            // Revenir à enum si nécessaire:
            // $table->enum('type', [...])->change();
        });
    }
};
