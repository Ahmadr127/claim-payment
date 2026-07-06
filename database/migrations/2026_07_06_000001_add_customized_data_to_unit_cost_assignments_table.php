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
        Schema::table('unit_cost_assignments', function (Blueprint $table) {
            $table->boolean('is_customized')->default(false)->after('is_active');
            $table->json('customized_data')->nullable()->after('is_customized');
            $table->timestamp('customized_at')->nullable()->after('customized_data');
            $table->foreignId('customized_by')->nullable()->constrained('users')->onDelete('set null')->after('customized_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_cost_assignments', function (Blueprint $table) {
            $table->dropColumn(['is_customized', 'customized_data', 'customized_at', 'customized_by']);
        });
    }
};
