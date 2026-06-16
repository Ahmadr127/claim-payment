<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id();
            $table->string('icd_code', 20)->unique()->comment('Kode ICD-10, ex: K35, K37');
            $table->string('name', 255)->comment('Nama diagnosa, ex: Akut Abdomen Anak');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('doctor_code', 20)->unique();
            $table->string('name', 150);
            $table->string('specialization', 150)->nullable()->comment('ex: Spesialis Anak, Bedah Umum');
            $table->string('sip_number', 50)->nullable()->comment('Nomor Surat Izin Praktek');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('insurance_providers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('ex: BPJS, JASA_RAHARJA, AXA');
            $table->string('name', 150);
            $table->string('contact_person')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('medical_record_number', 30)->unique()->comment('Nomor rekam medis');
            $table->string('name', 150);
            $table->string('nik', 16)->nullable()->unique()->comment('Nomor KTP');
            $table->enum('gender', ['L', 'P']);
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->foreignId('insurance_provider_id')->nullable()->constrained('insurance_providers')->nullOnDelete();
            $table->string('insurance_number', 50)->nullable()->comment('Nomor kartu asuransi/BPJS');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
        Schema::dropIfExists('insurance_providers');
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('diagnoses');
    }
};
