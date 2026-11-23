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
        // 1. Sesi Presensi Pembelajaran (Attendance Sessions)
        if (!Schema::hasTable('attendance_sessions')) {
            Schema::create('attendance_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('jadwal_id')->constrained('jadwals')->cascadeOnDelete();
                $table->foreignId('kelas_mapel_id')->nullable()->constrained('kelas_mapels')->nullOnDelete();
                $table->string('judul_sesi');
                $table->date('tanggal');
                $table->time('jam_mulai');
                $table->time('jam_selesai');
                $table->datetime('waktu_buka_presensi')->nullable();
                $table->datetime('waktu_tutup_presensi')->nullable();
                $table->integer('toleransi_terlambat_menit')->default(15);
                $table->enum('status', ['Draft', 'Aktif', 'Selesai', 'Dibatalkan'])->default('Draft');
                $table->string('qr_secret_key', 64)->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['tanggal', 'status'], 'att_sess_tgl_stat_idx');
                $table->index(['jadwal_id', 'status'], 'att_sess_jdw_stat_idx');
            });
        }

        // 2. Token QR Code Dinamis (Attendance QR Tokens)
        if (!Schema::hasTable('attendance_qr_tokens')) {
            Schema::create('attendance_qr_tokens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('attendance_session_id')->constrained('attendance_sessions')->cascadeOnDelete();
                $table->string('token_hash', 64)->unique();
                $table->datetime('expired_at');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['attendance_session_id', 'is_active', 'expired_at'], 'att_qr_sess_act_exp_idx');
            });
        }

        // 3. Kode Akses & PIN Peserta Portal (Participant Access Codes)
        if (!Schema::hasTable('participant_access_codes')) {
            Schema::create('participant_access_codes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('peserta_id')->unique()->constrained('pesertas')->cascadeOnDelete();
                $table->string('kode_peserta', 50)->unique();
                $table->string('pin_hash');
                $table->boolean('is_active')->default(true);
                $table->datetime('last_login_at')->nullable();
                $table->timestamps();
            });
        }

        // 4. Perluas Tabel Presensi Eksisting (Attendances)
        if (Schema::hasTable('presensis')) {
            Schema::table('presensis', function (Blueprint $table) {
                if (!Schema::hasColumn('presensis', 'attendance_session_id')) {
                    $table->foreignId('attendance_session_id')->nullable()->after('id')->constrained('attendance_sessions')->nullOnDelete();
                }
                if (!Schema::hasColumn('presensis', 'metode_presensi')) {
                    $table->enum('metode_presensi', ['QR', 'Manual', 'Sistem'])->default('Manual')->after('status_kehadiran');
                }
                if (!Schema::hasColumn('presensis', 'waktu_check_in')) {
                    $table->datetime('waktu_check_in')->nullable()->after('metode_presensi');
                }
                if (!Schema::hasColumn('presensis', 'keterangan')) {
                    $table->text('keterangan')->nullable()->after('waktu_check_in');
                }
                if (!Schema::hasColumn('presensis', 'diinput_oleh')) {
                    $table->foreignId('diinput_oleh')->nullable()->after('keterangan')->constrained('users')->nullOnDelete();
                }

                // Ubah status_kehadiran menjadi string/varchar agar mendukung 6 status:
                // Hadir, Terlambat, Izin, Sakit, Alpa, Dispensasi
                $table->string('status_kehadiran', 30)->default('Hadir')->change();
            });
        }

        // 5. Pengajuan Izin, Sakit & Dispensasi (Attendance Requests)
        if (!Schema::hasTable('attendance_requests')) {
            Schema::create('attendance_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('peserta_id')->constrained('pesertas')->cascadeOnDelete();
                $table->foreignId('attendance_session_id')->constrained('attendance_sessions')->cascadeOnDelete();
                $table->enum('jenis_pengajuan', ['Izin', 'Sakit', 'Dispensasi']);
                $table->text('alasan');
                $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
                $table->text('catatan_admin')->nullable();
                $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->datetime('diverifikasi_pada')->nullable();
                $table->timestamps();

                $table->index(['peserta_id', 'status'], 'att_req_pes_stat_idx');
                $table->index(['attendance_session_id', 'status'], 'att_req_sess_stat_idx');
            });
        }

        // 6. Bukti File Ketidakhadiran (Attendance Evidences)
        if (!Schema::hasTable('attendance_evidences')) {
            Schema::create('attendance_evidences', function (Blueprint $table) {
                $table->id();
                $table->foreignId('attendance_request_id')->constrained('attendance_requests')->cascadeOnDelete();
                $table->string('nama_file_asli');
                $table->string('path_file');
                $table->string('mime_type', 100);
                $table->unsignedBigInteger('file_size');
                $table->string('keterangan')->nullable();
                $table->timestamps();
            });
        }

        // 7. Dokumentasi Foto Kegiatan Pembelajaran (Class Documentations)
        if (!Schema::hasTable('class_documentations')) {
            Schema::create('class_documentations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('attendance_session_id')->nullable()->constrained('attendance_sessions')->nullOnDelete();
                $table->foreignId('kelas_mapel_id')->constrained('kelas_mapels')->cascadeOnDelete();
                $table->string('nama_file_asli');
                $table->string('path_file');
                $table->string('mime_type', 100);
                $table->enum('kategori', ['Kegiatan', 'Pengajar', 'Peserta', 'Praktik', 'Diskusi'])->default('Kegiatan');
                $table->text('keterangan')->nullable();
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['kelas_mapel_id', 'kategori'], 'class_doc_kelas_kat_idx');
            });
        }

        // 8. Riwayat & Audit Log Perubahan Presensi (Attendance Logs)
        if (!Schema::hasTable('attendance_logs')) {
            Schema::create('attendance_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('presensi_id')->nullable()->constrained('presensis')->cascadeOnDelete();
                $table->foreignId('attendance_session_id')->nullable()->constrained('attendance_sessions')->nullOnDelete();
                $table->foreignId('peserta_id')->nullable()->constrained('pesertas')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status_sebelum', 30)->nullable();
                $table->string('status_sesudah', 30);
                $table->text('alasan_perubahan')->nullable();
                $table->string('metode_input', 50)->default('Manual');
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->index(['presensi_id', 'created_at'], 'att_log_pres_cr_idx');
                $table->index(['peserta_id', 'attendance_session_id'], 'att_log_pes_sess_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('class_documentations');
        Schema::dropIfExists('attendance_evidences');
        Schema::dropIfExists('attendance_requests');

        if (Schema::hasTable('presensis')) {
            Schema::table('presensis', function (Blueprint $table) {
                if (Schema::hasColumn('presensis', 'attendance_session_id')) {
                    $table->dropForeign(['attendance_session_id']);
                    $table->dropColumn('attendance_session_id');
                }
                if (Schema::hasColumn('presensis', 'metode_presensi')) {
                    $table->dropColumn('metode_presensi');
                }
                if (Schema::hasColumn('presensis', 'waktu_check_in')) {
                    $table->dropColumn('waktu_check_in');
                }
                if (Schema::hasColumn('presensis', 'keterangan')) {
                    $table->dropColumn('keterangan');
                }
                if (Schema::hasColumn('presensis', 'diinput_oleh')) {
                    $table->dropForeign(['diinput_oleh']);
                    $table->dropColumn('diinput_oleh');
                }
            });
        }

        Schema::dropIfExists('participant_access_codes');
        Schema::dropIfExists('attendance_qr_tokens');
        Schema::dropIfExists('attendance_sessions');
    }
};
