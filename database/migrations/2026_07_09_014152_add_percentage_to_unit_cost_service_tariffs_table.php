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
        Schema::table('unit_cost_service_tariffs', function (Blueprint $table) {
            $table->decimal('percentage', 5, 2)->nullable()->after('amount')->comment('Persentase SVC khusus unit cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_cost_service_tariffs', function (Blueprint $table) {
            $table->dropColumn('percentage');
        });
    }
};
