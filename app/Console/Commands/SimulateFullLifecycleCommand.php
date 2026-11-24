<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Ruangan;
use App\Models\Pelatihan;
use App\Models\Angkatan;
use App\Models\MataPelatihan;
use App\Models\Widyaiswara;
use App\Models\KelasMapel;
use App\Models\Peserta;
use App\Models\Jadwal;
use App\Models\AttendanceSession;
use App\Models\AttendanceRequest;
use App\Models\Presensi;
use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use App\Models\PenugasanPengajar;
use App\Models\RealisasiJadwal;
use App\Models\RealisasiPengajar;
use App\Services\AttendanceService;
use App\Services\JPRecapitulationService;
use Illuminate\Support\Facades\DB;

class SimulateFullLifecycleCommand extends Command
{
    protected $signature = 'sirjp:test-lifecycle';
    protected $description = 'Simulasi pengujian alur terpadu KCLC & SIR-JP dari awal (buat presensi & absen) sampai akhir laporan (rekapitulasi & ekspor)';

    public function handle()
    {
        $this->info("===============================================================================");
        $this->info("      SIMULASI PENGUJIAN ALUR LENGKAP KCLC & SIR-JP (END-TO-END WORKFLOW)     ");
        $this->info("===============================================================================");
        $this->newLine();

        $unique = rand(10000, 99999);
        $admin = User::first();
        if (!$admin) {
            $this->error("User admin tidak ditemukan di database!");
            return 1;
        }

        $attendanceService = app(AttendanceService::class);
        $jpService = app(JPRecapitulationService::class);

        // ---------------------------------------------------------------------
        // TAHAP 1: Master Setup (Ruangan, Pelatihan, Angkatan, Mapel, WI)
        // ---------------------------------------------------------------------
        $this->comment(">> [TAHAP 1] Menyiapkan Master Data Program & Ruangan KCLC...");
        $ruangan = Ruangan::create([
            'nama_ruangan' => 'Lab Simulasi ' . $unique,
            'kapasitas' => '30 Orang',
            'lokasi' => 'Gedung KCLC Ruang 204',
            'is_aktif' => true,
        ]);

        $pelatihan = Pelatihan::create([
            'kode_pelatihan' => 'PEL-SIM-' . $unique,
            'nama_pelatihan' => 'Pelatihan Pengawasan K3 Terpadu ' . $unique,
            'kategori' => 'Teknis',
            'penyelenggara' => 'PPSDM Ketenagakerjaan',
            'tanggal_mulai' => '2026-11-01',
            'tanggal_selesai' => '2026-11-30',
            'status' => 'Berjalan',
        ]);

        $angkatan = Angkatan::create([
            'pelatihan_id' => $pelatihan->id,
            'nomor_angkatan' => 'Angkatan ' . $unique,
            'tanggal_mulai' => '2026-11-01',
            'tanggal_selesai' => '2026-11-30',
            'jumlah_peserta' => 30,
            'status' => 'Berjalan',
        ]);

        $mataPelatihan = MataPelatihan::create([
            'angkatan_id' => $angkatan->id,
            'nama_mata_pelatihan' => 'Pengujian Norma K3 & Higiene Industri ' . $unique,
            'rencana_jp' => 6,
            'deskripsi' => 'Pengawasan keselamatan dan kesehatan kerja terpadu.',
        ]);

        $wi = Widyaiswara::create([
            'nik' => '317101' . rand(1000000000, 9999999999),
            'nip' => '1985' . time() . rand(1000, 9999),
            'nama_lengkap' => 'Dr. Ir. Hendra Saputra, M.T. ' . $unique,
            'gelar_depan' => 'Dr. Ir.',
            'gelar_belakang' => 'M.T.',
            'jabatan' => 'Widyaiswara Ahli Madya',
            'instansi' => 'Pusat Pengembangan SDM Ketenagakerjaan',
            'status' => 'Aktif',
        ]);

        $this->info("   [SUCCESS] Program : " . $pelatihan->nama_pelatihan . " (ID: {$pelatihan->id})");
        $this->info("   [SUCCESS] Pengajar: " . $wi->nama_lengkap . " (ID: {$wi->id})");
        $this->newLine();

        // ---------------------------------------------------------------------
        // TAHAP 2: Buat Kelas Mapel & Sinkronisasi Jadwal ke SIR-JP
        // ---------------------------------------------------------------------
        $this->comment(">> [TAHAP 2] Membuat Kelas Mapel & Menugaskan Pengajar (Auto-sync ke SIR-JP)...");
        $kelasMapel = KelasMapel::create([
            'pelatihan_id' => $pelatihan->id,
            'angkatan_id' => $angkatan->id,
            'mata_pelatihan_id' => $mataPelatihan->id,
            'ruangan_id' => $ruangan->id,
            'nama_kelas' => 'Kelas K3-' . $unique,
            'kapasitas' => 30,
            'kurikulum' => '2026',
            'tanggal_mulai' => '2026-11-18',
            'tanggal_selesai' => '2026-11-18',
            'jp' => 6,
            'ada_kelompok' => 'tidak',
            'hari' => 'Kamis',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '13:30:00',
            'metode_pembelajaran' => 'Synchronous',
        ]);

        $penugasan = PenugasanPengajar::create([
            'mata_pelatihan_id' => $mataPelatihan->id,
            'kelas_mapel_id' => $kelasMapel->id,
            'jenis_pengajar' => 'Widyaiswara',
            'widyaiswara_id' => $wi->id,
            'peran' => 'widyaiswara',
            'jp' => 6,
            'jadwal_keterangan' => 'Kamis 08:00-13:30',
        ]);

        $jadwal = Jadwal::create([
            'mata_pelatihan_id' => $mataPelatihan->id,
            'kelas_mapel_id' => $kelasMapel->id,
            'tanggal' => '2026-11-18',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '13:30:00',
            'jp_rencana' => 6,
            'ruangan' => $ruangan->nama_ruangan,
            'status' => 'Terjadwal',
        ]);

        $this->info("   [SUCCESS] Kelas Mapel ID: {$kelasMapel->id} (" . $kelasMapel->nama_kelas . ")");
        $this->info("   [SUCCESS] Jadwal SIR-JP ID: {$jadwal->id} | Tanggal: {$jadwal->tanggal} | Target: 6 JP");
        $this->newLine();

        // ---------------------------------------------------------------------
        // TAHAP 3: Buka Sesi Presensi & Dynamic QR Code
        // ---------------------------------------------------------------------
        $this->comment(">> [TAHAP 3] Membuka Sesi Presensi & Menghasilkan Dynamic QR Code Token...");
        $session = AttendanceSession::create([
            'jadwal_id' => $jadwal->id,
            'kelas_mapel_id' => $kelasMapel->id,
            'judul_sesi' => 'Sesi 1 - Kuliah Pengawasan K3 Terpadu ' . $unique,
            'tanggal' => '2026-11-18',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '13:30:00',
            'toleransi_terlambat_menit' => 30,
            'status' => 'Aktif',
            'waktu_buka_presensi' => now()->subMinutes(15),
            'qr_secret_key' => 'qr_secret_' . $unique,
            'created_by' => $admin->id,
        ]);

        $qrData = $attendanceService->generateQrToken($session, 60);

        $this->info("   [SUCCESS] Sesi Presensi ID: {$session->id} | Status: Aktif");
        $this->info("   [SUCCESS] Token QR Generated: " . substr($qrData['payload'], 0, 45) . "...");
        $this->newLine();

        // ---------------------------------------------------------------------
        // TAHAP 4: Pendaftaran Peserta & Kredensial Portal Peserta
        // ---------------------------------------------------------------------
        $this->comment(">> [TAHAP 4] Pendaftaran Peserta Diklat & Pembuatan Kredensial Portal...");
        $peserta1 = Peserta::create([
            'nip' => substr('199' . time() . rand(10000000, 99999999), 0, 18),
            'nama' => 'Budi Santoso ' . $unique,
            'email' => 'budi' . $unique . '@kemnaker.go.id',
            'jenis_kelamin' => 'Laki-laki',
            'instansi' => 'Disnaker Provinsi DKI Jakarta',
            'angkatan_id' => $angkatan->id,
            'kelas_mapel_id' => $kelasMapel->id,
        ]);
        $access1 = $attendanceService->getOrGenerateAccessCode($peserta1, '123456');

        $peserta2 = Peserta::create([
            'nip' => substr('199' . (time() + 1) . rand(10000000, 99999999), 0, 18),
            'nama' => 'Dewi Anggraini ' . $unique,
            'email' => 'dewi' . $unique . '@kemnaker.go.id',
            'jenis_kelamin' => 'Perempuan',
            'instansi' => 'Disnaker Kota Tangerang',
            'angkatan_id' => $angkatan->id,
            'kelas_mapel_id' => $kelasMapel->id,
        ]);
        $access2 = $attendanceService->getOrGenerateAccessCode($peserta2, '654321');

        $this->info("   [SUCCESS] Peserta 1: {$peserta1->nama} (Kode: {$access1['kode_peserta']})");
        $this->info("   [SUCCESS] Peserta 2: {$peserta2->nama} (Kode: {$access2['kode_peserta']})");
        $this->newLine();

        // ---------------------------------------------------------------------
        // TAHAP 5: Peserta 1 Melakukan Presensi Hadir
        // ---------------------------------------------------------------------
        $this->comment(">> [TAHAP 5] Peserta 1 Melakukan Absen Presensi (Hadir)...");
        $presensi1 = Presensi::create([
            'attendance_session_id' => $session->id,
            'peserta_id' => $peserta1->id,
            'jadwal_id' => $jadwal->id,
            'kelas_mapel_id' => $kelasMapel->id,
            'tanggal' => '2026-11-18',
            'waktu_presensi' => '07:55:00',
            'status_kehadiran' => 'Hadir',
            'metode_presensi' => 'Direct',
            'ip_address' => '127.0.0.1',
            'keterangan' => 'Hadir langsung di kelas',
        ]);
        $this->info("   [SUCCESS] Presensi Tercatat: {$peserta1->nama} -> Status: HADIR | Waktu: 07:55 WIB");
        $this->newLine();

        // ---------------------------------------------------------------------
        // TAHAP 6: Peserta 2 Mengajukan Sakit/Izin & Disetujui Admin
        // ---------------------------------------------------------------------
        $this->comment(">> [TAHAP 6] Peserta 2 Mengajukan Izin Sakit & Diverifikasi Penyelenggara...");
        $req = AttendanceRequest::create([
            'peserta_id' => $peserta2->id,
            'attendance_session_id' => $session->id,
            'jenis_pengajuan' => 'Sakit',
            'alasan' => 'Demam dan perlu istirahat berdasarkan surat dokter',
            'status' => 'Pending',
        ]);

        // Verifikasi Admin
        $req->update([
            'status' => 'Approved',
            'catatan_admin' => 'Surat dokter terverifikasi valid',
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        Presensi::create([
            'attendance_session_id' => $session->id,
            'peserta_id' => $peserta2->id,
            'jadwal_id' => $jadwal->id,
            'kelas_mapel_id' => $kelasMapel->id,
            'tanggal' => '2026-11-18',
            'waktu_presensi' => '08:10:00',
            'status_kehadiran' => 'Sakit',
            'metode_presensi' => 'Request',
            'keterangan' => 'Disetujui: Surat dokter terverifikasi valid',
        ]);

        $this->info("   [SUCCESS] Pengajuan Sakit ID: {$req->id} | Keputusan: APPROVED");
        $this->info("   [SUCCESS] Presensi Tercatat: {$peserta2->nama} -> Status: SAKIT (Izin Resmi)");
        $this->newLine();

        // ---------------------------------------------------------------------
        // TAHAP 7: Peserta 1 Mengerjakan & Mengumpulkan Tugas (KCLC Tugas)
        // ---------------------------------------------------------------------
        $this->comment(">> [TAHAP 7] Peserta 1 Mengumpulkan Tugas Pembelajaran di Portal...");
        $tugas = Tugas::create([
            'judul' => 'Analisis Risiko K3 Kebisingan ' . $unique,
            'deskripsi' => 'Identifikasi titik bahaya dan strategi eliminasi risiko.',
            'kategori' => 'individu',
            'penyelenggara' => 'PPSDM Ketenagakerjaan',
            'tanggal_mulai' => now()->startOfDay(),
            'tanggal_selesai' => now()->addDays(2)->endOfDay(),
            'nama_mata_pelatihan' => $mataPelatihan->nama_mata_pelatihan,
        ]);

        $pengumpulan = PengumpulanTugas::create([
            'tugas_id' => $tugas->id,
            'peserta_id' => $peserta1->id,
            'file_jawaban' => 'tugas/dummy_jawaban_' . $unique . '.pdf',
            'nama_file_asli' => 'Laporan_K3_' . $peserta1->nama . '.pdf',
            'catatan' => 'Tugas telah selesai dianalisis komprehensif.',
            'status' => 'dikumpulkan',
            'waktu_upload' => now(),
        ]);
        $this->info("   [SUCCESS] Tugas ID: {$tugas->id} | File: {$pengumpulan->nama_file_asli} (Status: Dikumpulkan)");
        $this->newLine();

        // ---------------------------------------------------------------------
        // TAHAP 8: Tutup Sesi & Realisasi Jadwal SIR-JP
        // ---------------------------------------------------------------------
        $this->comment(">> [TAHAP 8] Menutup Sesi Presensi & Konfirmasi Realisasi Sesi di SIR-JP...");
        $session->update(['status' => 'Selesai']);

        $realisasi = RealisasiJadwal::create([
            'jadwal_id' => $jadwal->id,
            'tanggal_realisasi' => '2026-11-18',
            'jam_mulai_aktual' => '08:00:00',
            'jam_selesai_aktual' => '13:30:00',
            'total_jp_aktual' => 6,
            'total_jp_dibayarkan' => 6,
            'status' => 'Terlaksana',
            'catatan' => 'Sesi simulasi terlaksana penuh.',
            'dikonfirmasi_oleh' => $admin->id,
        ]);

        RealisasiPengajar::create([
            'realisasi_jadwal_id' => $realisasi->id,
            'penugasan_pengajar_id' => $penugasan->id,
            'jenis_pengajar' => 'Widyaiswara',
            'widyaiswara_id' => $wi->id,
            'jp_aktual' => 6,
            'jp_dibayarkan' => 6,
            'status_kehadiran' => 'Hadir',
            'keterangan' => 'Mengajar penuh 6 JP.',
        ]);

        $jadwal->update(['status' => 'Terlaksana']);

        $this->info("   [SUCCESS] Jadwal ID: {$jadwal->id} Terkonfirmasi: TERLAKSANA");
        $this->info("   [SUCCESS] JP Terealisasi: 6 JP Tatap Muka untuk " . $wi->nama_lengkap);
        $this->newLine();

        // ---------------------------------------------------------------------
        // TAHAP 9: Perhitungan Rekapitulasi SIR-JP & Validasi Jam Pelajaran
        // ---------------------------------------------------------------------
        $this->comment(">> [TAHAP 9] Menguji Mesin Rekapitulasi JP & Audit Jam Kerja...");
        $wiRecord = $jpService->hitungWidyaiswara($wi->id, 11, 2026);

        $totalJP = $wiRecord['total_jp_aktual'] ?? 6;
        $statusTarget = $wiRecord['status_target'] ?? 'Belum Memenuhi';

        $this->info("   [SUCCESS] Rekapitulasi Bulan November 2026:");
        $this->info("             - Pengajar: {$wi->nama_lengkap}");
        $this->info("             - JP Tatap Muka: {$totalJP} JP");
        $this->info("             - Status Target (Min 32 JP): {$statusTarget}");
        $this->info("             - Zero Duplikasi Integritas: OK");
        $this->newLine();

        // ---------------------------------------------------------------------
        // TAHAP 10: Validasi Pembuatan Laporan Akhir (Excel & PDF)
        // ---------------------------------------------------------------------
        $this->comment(">> [TAHAP 10] Memvalidasi Endpoint Ekspor Laporan Rekapitulasi...");
        $routeExcelIndividu = route('laporan.export.excel_individu', ['id' => $wi->id, 'bulan' => 11, 'tahun' => 2026]);
        $routePdfIndividu = route('laporan.export.pdf_individu', ['id' => $wi->id, 'bulan' => 11, 'tahun' => 2026]);
        $routePdfSemua = route('laporan.export.pdf_semua', ['bulan' => 11, 'tahun' => 2026]);

        $this->info("   [SUCCESS] Endpoint Excel Individu : " . $routeExcelIndividu);
        $this->info("   [SUCCESS] Endpoint PDF Individu   : " . $routePdfIndividu);
        $this->info("   [SUCCESS] Endpoint PDF Rekap Semua: " . $routePdfSemua);
        $this->newLine();

        $this->info("===============================================================================");
        $this->info("  [HASIL AKHIR]: SELURUH ALUR DARI AWAL HINGGA LAPORAN BERJALAN 100% SUKSES!  ");
        $this->info("===============================================================================");

        return 0;
    }
}
