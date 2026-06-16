<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Biaya kamar per hari rawat (direcord tiap hari atau saat discharge)
         */
        Schema::create('room_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospitalization_id')->constrained('hospitalizations')->restrictOnDelete();
            $table->foreignId('room_tariff_type_id')->constrained('room_tariff_types')->restrictOnDelete();
            $table->foreignId('room_tariff_id')->constrained('room_tariffs')->restrictOnDelete();
            $table->unsignedSmallInteger('qty')->default(1)->comment('Jumlah hari');
            $table->bigInteger('unit_price')->unsigned()->comment('Harga per hari saat itu');
            $table->bigInteger('total_price')->unsigned()->comment('qty x unit_price');
            $table->date('charge_date')->comment('Tanggal charge');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        /**
         * Biaya jasa medis (visit dokter, lab, radiologi, tindakan)
         */
        Schema::create('service_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospitalization_id')->constrained('hospitalizations')->restrictOnDelete();
            $table->foreignId('medical_service_id')->constrained('medical_services')->restrictOnDelete();
            $table->foreignId('service_tariff_id')->constrained('service_tariffs')->restrictOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
            $table->unsignedSmallInteger('qty')->default(1);
            $table->bigInteger('unit_price')->unsigned();
            $table->bigInteger('total_price')->unsigned();
            $table->date('charge_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        /**
         * Biaya obat dan alkes (consumable)
         */
        Schema::create('medication_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospitalization_id')->constrained('hospitalizations')->restrictOnDelete();
            $table->foreignId('medication_id')->constrained('medications')->restrictOnDelete();
            $table->foreignId('medication_tariff_id')->constrained('medication_tariffs')->restrictOnDelete();
            $table->unsignedSmallInteger('qty')->default(1);
            $table->bigInteger('unit_price')->unsigned();
            $table->bigInteger('total_price')->unsigned();
            $table->date('charge_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_charges');
        Schema::dropIfExists('service_charges');
        Schema::dropIfExists('room_charges');
    }
};
