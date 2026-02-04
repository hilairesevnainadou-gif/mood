<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pour MySQL, on modifie l'enum
        DB::statement("ALTER TABLE users MODIFY COLUMN member_status ENUM('pending', 'pending_documents', 'active', 'suspended', 'inactive') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN member_status ENUM('pending', 'active', 'suspended', 'inactive') DEFAULT 'pending'");
    }
};
