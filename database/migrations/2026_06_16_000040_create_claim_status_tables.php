<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Status klaim disimpan di database.
         * Nilai awal di-seed: draft, submitted, verified, approved, rejected, paid.
         * Admin bisa tambah status kustom tanpa ubah kode.
         */
        Schema::create('claim_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('ex: draft, submitted, verified, approved, rejected, paid');
            $table->string('name', 100);
            $table->string('color', 30)->default('gray')->comment('Warna badge UI: gray, blue, yellow, green, red, emerald');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->boolean('is_terminal')->default(false)->comment('True = status akhir (tidak bisa transition lagi)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        /**
         * Aturan transisi antar status — juga di database,
         * sehingga alur workflow bisa diubah admin tanpa deploy ulang.
         */
        Schema::create('claim_status_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_status_id')->constrained('claim_statuses')->restrictOnDelete();
            $table->foreignId('to_status_id')->constrained('claim_statuses')->restrictOnDelete();
            $table->string('required_role', 100)->nullable()->comment('Role yang boleh melakukan transisi ini');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['from_status_id', 'to_status_id'], 'transition_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_status_transitions');
        Schema::dropIfExists('claim_statuses');
    }
};
