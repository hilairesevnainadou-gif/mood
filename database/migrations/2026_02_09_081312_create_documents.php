<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Index composites critiques pour les performances
        Schema::table('documents', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_user_status');
            $table->index(['status', 'created_at'], 'idx_status_created');
            $table->index(['user_id', 'is_profile_document', 'type'], 'idx_user_profile_type');
            $table->index(['funding_request_id', 'is_profile_document'], 'idx_funding_profile');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('member_type');
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('idx_user_status');
            $table->dropIndex('idx_status_created');
            $table->dropIndex('idx_user_profile_type');
            $table->dropIndex('idx_funding_profile');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['member_type']);
        });
    }
};
