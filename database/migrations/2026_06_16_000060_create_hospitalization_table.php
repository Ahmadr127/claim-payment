<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospitalizations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number', 30)->unique()->comment('Nomor registrasi rawat inap');
            $table->foreignId('patient_id')->constrained('patients')->restrictOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->restrictOnDelete();
            $table->foreignId('room_class_id')->constrained('room_classes')->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->restrictOnDelete();
            $table->foreignId('diagnosis_id')->nullable()->constrained('diagnoses')->nullOnDelete();
            $table->text('diagnosis_notes')->nullable()->comment('Catatan diagnosa tambahan');
            $table->dateTime('admitted_at')->comment('Tanggal & jam masuk');
            $table->dateTime('discharged_at')->nullable()->comment('Tanggal & jam keluar (null = masih dirawat)');
            $table->unsignedSmallInteger('total_days')->default(0)->comment('Jumlah hari rawat (dihitung saat discharge)');
            $table->string('status', 30)->default('active')->comment('active, discharged, transferred');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_id', 'status']);
            $table->index(['admitted_at', 'discharged_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospitalizations');
    }
};
