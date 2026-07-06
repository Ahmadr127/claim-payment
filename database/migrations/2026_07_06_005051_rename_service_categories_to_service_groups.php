<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop foreign key constraint on medical_services
        Schema::table('medical_services', function (Blueprint $table) {
            $table->dropForeign(['service_category_id']);
        });

        // 2. Rename the table
        Schema::rename('service_categories', 'service_groups');

        // 3. Rename the column in medical_services and re-add constraint
        Schema::table('medical_services', function (Blueprint $table) {
            $table->renameColumn('service_category_id', 'service_group_id');
        });

        Schema::table('medical_services', function (Blueprint $table) {
            $table->foreign('service_group_id')->references('id')->on('service_groups')->restrictOnDelete();
        });

        // 4. Add audit columns to service_groups
        Schema::table('service_groups', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Drop audit columns
        Schema::table('service_groups', function (Blueprint $table) {
            $table->dropForeign(['service_groups_created_by_foreign']);
            $table->dropForeign(['service_groups_updated_by_foreign']);
            $table->dropColumn(['created_by', 'updated_by']);
        });

        Schema::table('medical_services', function (Blueprint $table) {
            $table->dropForeign(['service_group_id']);
        });

        Schema::table('medical_services', function (Blueprint $table) {
            $table->renameColumn('service_group_id', 'service_category_id');
        });

        Schema::rename('service_groups', 'service_categories');

        Schema::table('medical_services', function (Blueprint $table) {
            $table->foreign('service_category_id')->references('id')->on('service_categories')->restrictOnDelete();
        });
    }
};
