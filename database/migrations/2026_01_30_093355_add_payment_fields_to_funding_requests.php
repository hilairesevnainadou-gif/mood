<?php
// 2024_01_29_000004_add_payment_fields_to_funding_requests.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('funding_requests', function (Blueprint $table) {
            $table->boolean('is_predefined')->default(false)->after('type');
            $table->foreignId('funding_type_id')->nullable()->after('is_predefined')->constrained('funding_types');
            $table->string('payment_motif', 4)->nullable()->after('funding_type_id');
            $table->decimal('expected_payment', 15, 2)->nullable()->after('payment_motif');
            $table->timestamp('validated_at')->nullable()->after('expected_payment');
            $table->foreignId('validated_by')->nullable()->after('validated_at')->constrained('users');
            $table->timestamp('paid_at')->nullable()->after('validated_at');
            $table->text('admin_validation_notes')->nullable();
        });
    }
    public function down(): void {
        Schema::table('funding_requests', function (Blueprint $table) {
            $table->dropColumn(['is_predefined', 'funding_type_id', 'payment_motif', 'expected_payment', 'validated_at', 'validated_by', 'paid_at', 'admin_validation_notes']);
        });
    }
};
