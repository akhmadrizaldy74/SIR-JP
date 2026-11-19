<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('realisasi_jadwals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->unique()->constrained('jadwals')->cascadeOnDelete();
            $table->date('tanggal_realisasi');
            $table->time('jam_mulai_aktual');
            $table->time('jam_selesai_aktual');
            $table->decimal('jp_aktual', 5, 2)->default(0.00);
            $table->decimal('jp_dibayarkan', 5, 2)->default(0.00);
            $table->enum('status', ['Terlaksana', 'Dibatalkan'])->default('Terlaksana');
            $table->text('alasan_batal')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('diverifikasi_pada')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tanggal_realisasi', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realisasi_jadwals');
    }
};
