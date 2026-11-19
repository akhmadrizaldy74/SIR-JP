<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelatihan_id')->constrained('mata_pelatihans')->cascadeOnDelete();
            $table->foreignId('penugasan_pengajar_id')->nullable()->constrained('penugasan_pengajars')->nullOnDelete();
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->unsignedInteger('sesi_ke')->nullable();
            $table->decimal('jp_rencana', 5, 2)->default(1.00);
            $table->string('ruangan')->nullable();
            $table->enum('metode', ['Tatap Muka / Klasikal', 'Daring / Online', 'Blended'])->default('Tatap Muka / Klasikal');
            $table->enum('status', ['Terjadwal', 'Terlaksana', 'Dibatalkan'])->default('Terjadwal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tanggal', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
