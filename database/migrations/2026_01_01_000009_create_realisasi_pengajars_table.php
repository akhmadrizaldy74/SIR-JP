<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('realisasi_pengajars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('realisasi_jadwal_id')->constrained('realisasi_jadwals')->cascadeOnDelete();
            $table->foreignId('penugasan_pengajar_id')->nullable()->constrained('penugasan_pengajars')->nullOnDelete();
            $table->enum('jenis_pengajar', ['Widyaiswara', 'Narasumber']);
            $table->foreignId('widyaiswara_id')->nullable()->constrained('widyaiswaras')->nullOnDelete();
            $table->foreignId('narasumber_id')->nullable()->constrained('narasumbers')->nullOnDelete();
            $table->decimal('jp_aktual', 5, 2)->default(0.00);
            $table->decimal('jp_dibayarkan', 5, 2)->default(0.00);
            $table->enum('status_kehadiran', ['Hadir', 'Izin', 'Sakit', 'Digantikan'])->default('Hadir');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['realisasi_jadwal_id', 'widyaiswara_id']);
            $table->index(['realisasi_jadwal_id', 'narasumber_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realisasi_pengajars');
    }
};
