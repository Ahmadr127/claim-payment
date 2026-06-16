<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Kelas kamar (SUITES, VVIP, VIP, dst) dikelola via admin,
         * tidak hardcode di kode — bisa ditambah/edit tanpa deploy ulang.
         */
        Schema::create('room_classes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Kode unik, ex: SUITES, VVIP, VIP, UTAMA, KELAS_I, II, III');
            $table->string('name', 100)->comment('Nama tampil, ex: Kelas I');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_class_id')->constrained('room_classes')->restrictOnDelete();
            $table->string('room_number', 20)->unique();
            $table->string('name', 100);
            $table->unsignedSmallInteger('capacity')->default(1);
            $table->boolean('is_occupied')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        /**
         * Tarif kamar & perawatan umum per kelas — bisa berubah tiap periode
         * tanpa perlu ubah kode.
         */
        Schema::create('room_tariff_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('ex: kamar_rawat, perawatan_umum');
            $table->string('name', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('room_tariffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_class_id')->constrained('room_classes')->restrictOnDelete();
            $table->foreignId('room_tariff_type_id')->constrained('room_tariff_types')->restrictOnDelete();
            $table->bigInteger('amount')->unsigned()->comment('Biaya dalam Rupiah (integer)');
            $table->date('effective_date')->comment('Berlaku mulai tanggal ini');
            $table->date('expired_date')->nullable()->comment('Null = masih berlaku');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['room_class_id', 'room_tariff_type_id', 'is_active', 'effective_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_tariffs');
        Schema::dropIfExists('room_tariff_types');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('room_classes');
    }
};
