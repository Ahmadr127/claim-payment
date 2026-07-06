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
        Schema::table('medications', function (Blueprint $table) {
            // Drop old ppn columns
            $table->dropColumn(['ppn_rajal', 'ppn_ranap']);
            
            // Add new unified ppn_percentage column
            $table->decimal('ppn_percentage', 5, 2)->default(11)->after('hna_ppn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            // Restore old columns
            $table->decimal('ppn_rajal', 5, 2)->default(0)->after('hna_ppn');
            $table->decimal('ppn_ranap', 5, 2)->default(0)->after('ppn_rajal');
            
            // Drop new column
            $table->dropColumn('ppn_percentage');
        });
    }
};
