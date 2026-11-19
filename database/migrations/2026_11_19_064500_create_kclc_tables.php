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
        // 1. Ruangan
        if (!Schema::hasTable('ruangans')) {
            Schema::create('ruangans', function (Blueprint $table) {
                $table->id();
                $table->string('nama_ruangan');
                $table->string('kapasitas')->nullable();
                $table->string('lokasi')->nullable();
                $table->boolean('is_aktif')->default(true);
                $table->timestamps();
            });
        }

        // 2. Kelas Mapel
        if (!Schema::hasTable('kelas_mapels')) {
            Schema::create('kelas_mapels', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pelatihan_id')->constrained('pelatihans')->cascadeOnDelete();
                $table->foreignId('angkatan_id')->constrained('angkatans')->cascadeOnDelete();
                $table->foreignId('mata_pelatihan_id')->nullable()->constrained('mata_pelatihans')->nullOnDelete();
                $table->foreignId('ruangan_id')->nullable()->constrained('ruangans')->nullOnDelete();
                $table->string('nama_kelas');
                $table->integer('kapasitas')->default(30);
                $table->string('kurikulum')->default('2026');
                $table->date('tanggal_mulai')->nullable();
                $table->date('tanggal_selesai')->nullable();
                $table->float('jp')->default(0);
                $table->enum('ada_kelompok', ['ya', 'tidak'])->default('tidak');
                $table->string('hari')->nullable();
                $table->time('jam_mulai')->nullable();
                $table->time('jam_selesai')->nullable();
                $table->string('metode_pembelajaran')->default('Synchronous'); // Synchronous, Asynchronous, Klasikal
                $table->timestamps();
            });
        }

        // 3. Kelas Mapel Pengajar (Relasi Pengajar & Peran)
        if (!Schema::hasTable('kelas_mapel_pengajars')) {
            Schema::create('kelas_mapel_pengajars', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kelas_mapel_id')->constrained('kelas_mapels')->cascadeOnDelete();
                $table->string('pengajar_type'); // Widyaiswara or Narasumber
                $table->unsignedBigInteger('pengajar_id');
                $table->float('jp')->default(0);
                $table->string('jadwal_keterangan')->nullable();
                $table->enum('peran', ['widyaiswara', 'narasumber', 'coach', 'mentor', 'evaluator'])->default('widyaiswara');
                $table->string('kelompok')->nullable();
                $table->timestamps();
            });
        }

        // 4. Peserta Diklat
        if (!Schema::hasTable('pesertas')) {
            Schema::create('pesertas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('angkatan_id')->nullable()->constrained('angkatans')->nullOnDelete();
                $table->foreignId('kelas_mapel_id')->nullable()->constrained('kelas_mapels')->nullOnDelete();
                $table->string('nip', 30)->unique();
                $table->string('nama');
                $table->string('email')->nullable();
                $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->default('Laki-laki');
                $table->string('instansi')->nullable();
                $table->string('jabatan')->nullable();
                $table->string('golongan')->nullable();
                $table->timestamps();
            });
        }

        // 5. Presensi Peserta
        if (!Schema::hasTable('presensis')) {
            Schema::create('presensis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('peserta_id')->constrained('pesertas')->cascadeOnDelete();
                $table->foreignId('jadwal_id')->nullable()->constrained('jadwals')->nullOnDelete();
                $table->foreignId('kelas_mapel_id')->nullable()->constrained('kelas_mapels')->nullOnDelete();
                $table->date('tanggal');
                $table->enum('status_kehadiran', ['Hadir', 'Izin', 'Sakit', 'Alpa'])->default('Hadir');
                $table->time('waktu_presensi')->nullable();
                $table->timestamps();
            });
        }

        // 6. Aspek Penilaian Sikap
        if (!Schema::hasTable('aspek_penilaian_sikaps')) {
            Schema::create('aspek_penilaian_sikaps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pelatihan_id')->constrained('pelatihans')->cascadeOnDelete();
                $table->string('kategori')->nullable(); // e.g. Nilai Sikap dari Coach
                $table->string('judul');
                $table->json('aspek_penilaian')->nullable(); // Array of criteria: ["Kedisiplinan", "Kepemimpinan", ...]
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 7. Form Evaluasi
        if (!Schema::hasTable('form_evaluasis')) {
            Schema::create('form_evaluasis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pelatihan_id')->constrained('pelatihans')->cascadeOnDelete();
                $table->string('judul_form');
                $table->text('deskripsi')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_evaluasis');
        Schema::dropIfExists('aspek_penilaian_sikaps');
        Schema::dropIfExists('presensis');
        Schema::dropIfExists('pesertas');
        Schema::dropIfExists('kelas_mapel_pengajars');
        Schema::dropIfExists('kelas_mapels');
        Schema::dropIfExists('ruangans');
    }
};
