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
        Schema::create('unit_cost_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnosis_id')->constrained('diagnoses')->onDelete('cascade');
            $table->foreignId('organization_unit_id')->constrained('organization_units')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            // Unique constraint: setiap diagnosis hanya bisa di-assign sekali ke satu unit
            $table->unique(['diagnosis_id', 'organization_unit_id']);

            // Indexes untuk query yang sering
            $table->index('organization_unit_id');
            $table->index('is_active');
            $table->index('assigned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_cost_assignments');
    }
};
