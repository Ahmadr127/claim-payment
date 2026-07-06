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
        Schema::table('room_tariff_types', function (Blueprint $table) {
            $table->decimal('hna', 15, 2)->nullable()->after('name')->comment('HNA untuk perhitungan');
            $table->decimal('ppn', 5, 2)->default(0)->after('hna')->comment('PPN percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_tariff_types', function (Blueprint $table) {
            $table->dropColumn(['hna', 'ppn']);
        });
    }
};
