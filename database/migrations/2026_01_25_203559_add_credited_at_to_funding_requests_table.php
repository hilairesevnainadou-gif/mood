
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('funding_requests', function (Blueprint $table) {
            $table->timestamp('credited_at')->nullable()->after('funded_at');
        });
    }

    public function down()
    {
        Schema::table('funding_requests', function (Blueprint $table) {
            $table->dropColumn('credited_at');
        });
    }
};