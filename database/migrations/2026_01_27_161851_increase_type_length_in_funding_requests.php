<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('funding_requests', function (Blueprint $table) {
        $table->string('type', 255)->change(); // Augmente à 255 caractères
    });
}

    // Dans la nouvelle migration


/**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('funding_requests', function (Blueprint $table) {
        $table->string('type', 50)->change(); // Revenir à la taille d'origine
    });
}
};
