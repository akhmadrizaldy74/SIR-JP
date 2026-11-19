<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konversi_jps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('widyaiswara_id')->constrained('widyaiswaras')->cascadeOnDelete();
            $table->date('tanggal_kegiatan');
            $table->string('nama_kegiatan');
            $table->string('kategori_konversi');
            $table->decimal('jumlah_jp', 5, 2);
            $table->decimal('jp_dibayarkan', 5, 2)->default(0.00);
            $table->string('informasi_pembayaran')->nullable();
            $table->string('nomor_sk')->nullable();
            $table->enum('status_validasi', ['Disetujui', 'Menunggu', 'Ditolak'])->default('Disetujui');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['widyaiswara_id', 'tanggal_kegiatan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konversi_jps');
    }
};
