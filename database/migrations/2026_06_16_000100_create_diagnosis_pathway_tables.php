<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnosis_pathways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnosis_id')->constrained('diagnoses')->restrictOnDelete();
            $table->unsignedSmallInteger('length_of_stay')->default(1)->comment('Lama rawat dalam hari');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('diagnosis_pathway_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnosis_pathway_id')->constrained('diagnosis_pathways')->cascadeOnDelete();
            $table->string('item_type', 100)->comment('App\Models\Room\RoomTariffType, App\Models\Patient\MedicalService, App\Models\Pharmacy\Medication');
            $table->unsignedBigInteger('item_id');
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->timestamps();

            $table->index(['item_type', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosis_pathway_items');
        Schema::dropIfExists('diagnosis_pathways');
    }
};
