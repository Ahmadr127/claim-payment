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
            $table->string('group_code', 50)->nullable()->after('unit');
            $table->string('group_name', 150)->nullable()->after('group_code');
            $table->string('commodity', 150)->nullable()->after('group_name');
            $table->string('product_group', 150)->nullable()->after('commodity');
            $table->decimal('hna', 15, 2)->nullable()->after('product_group');
            $table->decimal('hna_ppn', 15, 2)->nullable()->after('hna');
            $table->decimal('ppn_rajal', 5, 2)->default(0.00)->after('hna_ppn');
            $table->decimal('ppn_ranap', 5, 2)->default(0.00)->after('ppn_rajal');
            $table->text('indication')->nullable()->after('ppn_ranap');
            $table->text('active_ingredient')->nullable()->after('indication');
            $table->text('detailed_composition')->nullable()->after('active_ingredient');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->dropColumn([
                'group_code',
                'group_name',
                'commodity',
                'product_group',
                'hna',
                'hna_ppn',
                'ppn_rajal',
                'ppn_ranap',
                'indication',
                'active_ingredient',
                'detailed_composition'
            ]);
        });
    }
};
