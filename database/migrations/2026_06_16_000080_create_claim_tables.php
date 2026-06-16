<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_number', 30)->unique()->comment('CLM-YYYYMM-XXXXX');
            $table->foreignId('hospitalization_id')->constrained('hospitalizations')->restrictOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->restrictOnDelete();
            $table->foreignId('room_class_id')->constrained('room_classes')->restrictOnDelete();
            $table->foreignId('insurance_provider_id')->nullable()->constrained('insurance_providers')->nullOnDelete();
            $table->foreignId('claim_status_id')->constrained('claim_statuses')->restrictOnDelete();

            // Agregasi total per kategori (snapshot saat klaim dibuat)
            $table->bigInteger('total_room_charge')->unsigned()->default(0);
            $table->bigInteger('total_service_charge')->unsigned()->default(0);
            $table->bigInteger('total_medication_charge')->unsigned()->default(0);
            $table->bigInteger('grand_total')->unsigned()->default(0);

            // Timestamps per status
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();

            // Audit who did what
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['claim_status_id', 'created_at']);
            $table->index(['patient_id', 'claim_status_id']);
            $table->index(['insurance_provider_id', 'claim_status_id']);
        });

        /**
         * Detail item klaim — snapshot dari setiap charge yang diklaim.
         * Tidak hilang meski data charge diedit setelah klaim dibuat.
         */
        Schema::create('claim_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained('claims')->cascadeOnDelete();
            $table->string('charge_type', 30)->comment('room, service, medication — referensi bebas, bukan FK ke enum');
            $table->unsignedBigInteger('charge_id')->comment('ID dari room_charges/service_charges/medication_charges');
            $table->string('item_code', 50)->nullable()->comment('Snapshot kode item saat klaim');
            $table->string('item_name', 255)->comment('Snapshot nama item saat klaim');
            $table->string('category_name', 150)->nullable()->comment('Snapshot nama kategori');
            $table->unsignedSmallInteger('qty');
            $table->bigInteger('unit_price')->unsigned();
            $table->bigInteger('total_price')->unsigned();
            $table->date('charge_date');
            $table->timestamps();

            $table->index(['claim_id', 'charge_type']);
        });

        /**
         * Riwayat perubahan status klaim (audit trail lengkap)
         */
        Schema::create('claim_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained('claims')->cascadeOnDelete();
            $table->foreignId('from_status_id')->nullable()->constrained('claim_statuses')->nullOnDelete();
            $table->foreignId('to_status_id')->constrained('claim_statuses')->restrictOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['claim_id', 'created_at']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number', 30)->unique();
            $table->foreignId('claim_id')->constrained('claims')->restrictOnDelete();
            $table->bigInteger('amount')->unsigned();
            $table->string('payment_method', 50)->comment('transfer, tunai, cek');
            $table->string('reference_number', 100)->nullable()->comment('Nomor referensi transfer/cek');
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('claim_status_histories');
        Schema::dropIfExists('claim_items');
        Schema::dropIfExists('claims');
    }
};
