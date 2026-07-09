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
        Schema::create('unit_cost_service_tariffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_service_id')->constrained('medical_services')->restrictOnDelete();
            $table->foreignId('room_class_id')->constrained('room_classes')->restrictOnDelete();
            $table->bigInteger('amount')->unsigned()->comment('Tarif dasar unit cost dalam Rupiah');
            $table->date('effective_date');
            $table->date('expired_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['medical_service_id', 'room_class_id', 'effective_date'], 'uc_service_tariff_unique');
            $table->index(['medical_service_id', 'room_class_id', 'is_active']);
        });

        Schema::create('unit_cost_medication_tariffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_id')->constrained('medications')->restrictOnDelete();
            $table->foreignId('room_class_id')->constrained('room_classes')->restrictOnDelete();
            $table->decimal('hna', 15, 2)->comment('HNA dasar unit cost');
            $table->decimal('ppn_percentage', 5, 2)->default(11)->comment('Persentase PPN unit cost');
            $table->bigInteger('amount')->unsigned()->comment('Tarif unit cost (HNA + PPN) dalam Rupiah');
            $table->date('effective_date');
            $table->date('expired_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['medication_id', 'room_class_id', 'effective_date'], 'uc_medication_tariff_unique');
            $table->index(['medication_id', 'room_class_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_cost_medication_tariffs');
        Schema::dropIfExists('unit_cost_service_tariffs');
    }
};
