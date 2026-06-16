<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Obat dan alkes (consumable) dengan kode item (OBTO/ALKO/ALK).
         * Bisa ditambah item baru via admin tanpa ubah kode.
         */
        Schema::create('medication_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('ex: OBAT, ALKES, INFUS, KONSUMABLE');
            $table->string('name', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_category_id')->constrained('medication_categories')->restrictOnDelete();
            $table->string('item_code', 20)->unique()->comment('ex: OBTO1259, ALK00701');
            $table->string('name', 200)->comment('Nama obat/alkes');
            $table->string('unit', 30)->default('pcs')->comment('Satuan: pcs, botol, ampul, strip');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        /**
         * Tarif obat/alkes per kelas kamar.
         * Ref tabel: TERFACEF INJ di SUITES=440rb, KELAS_I=411rb
         */
        Schema::create('medication_tariffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_id')->constrained('medications')->restrictOnDelete();
            $table->foreignId('room_class_id')->constrained('room_classes')->restrictOnDelete();
            $table->bigInteger('amount')->unsigned()->comment('Harga per unit dalam Rupiah');
            $table->date('effective_date');
            $table->date('expired_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['medication_id', 'room_class_id', 'effective_date'], 'medication_tariff_unique');
            $table->index(['medication_id', 'room_class_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_tariffs');
        Schema::dropIfExists('medications');
        Schema::dropIfExists('medication_categories');
    }
};
