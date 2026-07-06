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
        Schema::table('medical_services', function (Blueprint $table) {
            $table->decimal('percentage', 5, 2)->default(70)->after('unit')->comment('Persentase dari tarif kamar (default 70%)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_services', function (Blueprint $table) {
            $table->dropColumn('percentage');
        });
    }
};
