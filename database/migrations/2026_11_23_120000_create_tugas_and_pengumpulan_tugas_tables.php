<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelatihan_id')->nullable()->constrained('mata_pelatihans')->nullOnDelete();
            $table->string('nama_mata_pelatihan')->nullable();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('kategori', ['individu', 'kelompok'])->default('individu');
            $table->string('penyelenggara')->default('PPSDM Ketenagakerjaan');
            $table->dateTime('tanggal_mulai')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();
            $table->string('file_materi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['kategori', 'tanggal_selesai']);
        });

        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugas')->cascadeOnDelete();
            $table->foreignId('peserta_id')->constrained('pesertas')->cascadeOnDelete();
            $table->string('file_jawaban');
            $table->string('nama_file_asli');
            $table->text('catatan')->nullable();
            $table->dateTime('waktu_upload');
            $table->string('status')->default('Sudah di upload');
            $table->integer('nilai')->nullable();
            $table->timestamps();

            $table->unique(['tugas_id', 'peserta_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
        Schema::dropIfExists('tugas');
    }
};
