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
        // 1. Create medication_groups table
        Schema::create('medication_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->nullable()->unique();
            $table->string('name', 150);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Create medication_commodities table
        Schema::create('medication_commodities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Create medication_product_groups table
        Schema::create('medication_product_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Update medications table
        Schema::table('medications', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn([
                'group_code',
                'group_name',
                'commodity',
                'product_group'
            ]);

            // Add foreign key columns
            $table->foreignId('medication_group_id')->nullable()->after('unit')->constrained('medication_groups')->nullOnDelete();
            $table->foreignId('medication_commodity_id')->nullable()->after('medication_group_id')->constrained('medication_commodities')->nullOnDelete();
            $table->foreignId('medication_product_group_id')->nullable()->after('medication_commodity_id')->constrained('medication_product_groups')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->dropForeign(['medication_group_id']);
            $table->dropForeign(['medication_commodity_id']);
            $table->dropForeign(['medication_product_group_id']);
            $table->dropColumn([
                'medication_group_id',
                'medication_commodity_id',
                'medication_product_group_id'
            ]);

            // Restore old columns
            $table->string('group_code', 50)->nullable()->after('unit');
            $table->string('group_name', 150)->nullable()->after('group_code');
            $table->string('commodity', 150)->nullable()->after('group_name');
            $table->string('product_group', 150)->nullable()->after('commodity');
        });

        Schema::dropIfExists('medication_product_groups');
        Schema::dropIfExists('medication_commodities');
        Schema::dropIfExists('medication_groups');
    }
};
