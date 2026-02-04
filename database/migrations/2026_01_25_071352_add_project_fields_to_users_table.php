<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Informations liées au projet de financement
            $table->string('project_type')->nullable()->after('member_type');
            $table->text('project_description')->nullable()->after('project_type');
            $table->decimal('funding_needed', 15, 2)->nullable()->after('project_description');
            $table->integer('expected_jobs')->default(0)->after('funding_needed');
            $table->integer('project_duration')->nullable()->after('expected_jobs'); // en mois
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'project_type',
                'project_description',
                'funding_needed',
                'expected_jobs',
                'project_duration',
            ]);
        });
    }
};
