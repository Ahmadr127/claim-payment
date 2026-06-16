<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Kategori jasa: Perawatan, Visit Dokter, Lab, Radiologi, dst.
         * Dikelola via admin — bisa tambah kategori baru tanpa ubah kode.
         */
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('ex: perawatan_umum, visit_spesialis, laboratorium, radiologi');
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        /**
         * Layanan medis spesifik (ex: "Visit Dokter Spesialis Anak", "USG Abdomen", "Hematologi Lengkap")
         */
        Schema::create('medical_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')->constrained('service_categories')->restrictOnDelete();
            $table->string('code', 50)->unique()->comment('Kode layanan internal');
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->string('unit', 30)->default('kali')->comment('Satuan: kali, hari, paket');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        /**
         * Tarif jasa per kelas kamar — bisa berbeda tiap kelas.
         * Ref tabel: Visit Spesialis Anak di SUITES=840rb, di KELAS_III=630rb
         */
        Schema::create('service_tariffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_service_id')->constrained('medical_services')->restrictOnDelete();
            $table->foreignId('room_class_id')->constrained('room_classes')->restrictOnDelete();
            $table->bigInteger('amount')->unsigned()->comment('Biaya dalam Rupiah');
            $table->date('effective_date');
            $table->date('expired_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['medical_service_id', 'room_class_id', 'effective_date'], 'service_tariff_unique');
            $table->index(['medical_service_id', 'room_class_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_tariffs');
        Schema::dropIfExists('medical_services');
        Schema::dropIfExists('service_categories');
    }
};
