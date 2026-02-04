<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position')->nullable()->after('company_type');
            }
            if (!Schema::hasColumn('users', 'registration_number')) {
                $table->string('registration_number')->nullable()->after('sector');
            }
            if (!Schema::hasColumn('users', 'secondary_email')) {
                $table->string('secondary_email')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'secondary_phone')) {
                $table->string('secondary_phone')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'project_name')) {
                $table->string('project_name')->nullable()->after('project_description');
            }
            if (!Schema::hasColumn('users', 'own_contribution')) {
                $table->decimal('own_contribution', 15, 2)->nullable()->after('funding_needed');
            }
            if (!Schema::hasColumn('users', 'expected_revenue')) {
                $table->decimal('expected_revenue', 15, 2)->nullable()->after('annual_turnover');
            }
            if (!Schema::hasColumn('users', 'account_type')) {
                $table->enum('account_type', ['particulier', 'entreprise'])->nullable()->after('member_type');
            }
            if (!Schema::hasColumn('users', 'terms_accepted')) {
                $table->boolean('terms_accepted')->default(false)->after('accepts_notifications');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'position',
                'registration_number',
                'secondary_email',
                'secondary_phone',
                'project_name',
                'own_contribution',
                'expected_revenue',
                'account_type',
                'terms_accepted'
            ]);
        });
    }
};