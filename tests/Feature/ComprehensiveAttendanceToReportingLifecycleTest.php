<?php

namespace Tests\Feature;

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
use App\Services\AttendanceService;
use App\Services\JPRecapitulationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ComprehensiveAttendanceToReportingLifecycleTest extends TestCase
{
    protected User $admin;
    protected AttendanceService $attendanceService;
    protected JPRecapitulationService $jpService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::first() ?? User::factory()->create();
        $this->attendanceService = app(AttendanceService::class);
        $this->jpService = app(JPRecapitulationService::class);
        Storage::fake('local');
    }

    public function test_full_attendance_to_reporting_lifecycle(): void
    {
        $unique = rand(10000, 99999);

        // =========================================================================
        // TAHAP 1: Inisialisasi Master Data & Program Pelatihan (KCLC)
        // =========================================================================
        $ruangan = Ruangan::create([
            'nama_ruangan' => 'Laboratorium Komputer ' . $unique,
            'kapasitas' => '35 Peserta',
            'lokasi' => 'Gedung KCLC Lantai 2',
            'is_aktif' => true,
        ]);
        $this->assertDatabaseHas('ruangans', ['id' => $ruangan->id]);

        $pelatihan = Pelatihan::create([
            'kode_pelatihan' => 'PEL-LIFECYCLE-' . $unique,
            'nama_pelatihan' => 'Pelatihan Pengawasan Ketenagakerjaan Terpadu ' . $unique,
            'kategori' => 'Teknis',
            'penyelenggara' => 'PPSDM Ketenagakerjaan',
            'tanggal_mulai' => '2026-11-01',
            'tanggal_selesai' => '2026-11-30',
            'status' => 'Berjalan',
        ]);
        $this->assertDatabaseHas('pelatihans', ['id' => $pelatihan->id]);

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
            'nama_mata_pelatihan' => 'Teknik Pemeriksaan K3 ' . $unique,
            'rencana_jp' => 6,
            'deskripsi' => 'Modul teknik pemeriksaan norma K3 dan investigasi kecelakaan kerja.',
        ]);

        $wi = Widyaiswara::create([
            'nik' => '317201' . rand(1000000000, 9999999999),
            'nip' => '1980' . time() . rand(1000, 9999),
            'nama_lengkap' => 'Ir. Hendro Wicaksono, M.T. ' . $unique,
            'gelar_depan' => 'Ir.',
            'gelar_belakang' => 'M.T.',
            'jabatan' => 'Widyaiswara Ahli Madya',
            'instansi' => 'Pusat Pengembangan SDM Kemnaker',
            'status' => 'Aktif',
        ]);

        // =========================================================================
        // TAHAP 2: Buat Kelas Mapel, Penugasan Pengajar & Sinkron Jadwal SIR-JP
        // =========================================================================
        $kelasMapel = KelasMapel::create([
            'pelatihan_id' => $pelatihan->id,
            'angkatan_id' => $angkatan->id,
            'mata_pelatihan_id' => $mataPelatihan->id,
            'ruangan_id' => $ruangan->id,
            'nama_kelas' => 'Kelas K3-' . $unique,
            'kapasitas' => 35,
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

        // Input penugasan pengajar via HTTP KCLC Tab 2
        $assignResponse = $this->actingAs($this->admin)->post(
            route('kelas-mapel.pengajar.store', $kelasMapel->id),
            [
                'pengajar_combo' => 'Widyaiswara:' . $wi->id,
                'jp' => 6,
                'peran' => 'widyaiswara',
                'jadwal_keterangan' => 'Kamis 08:00-13:30',
            ]
        );
        $assignResponse->assertRedirect();

        // Verifikasi terdaftar di SIR-JP Penugasan Pengajar
        $this->assertDatabaseHas('penugasan_pengajars', [
            'mata_pelatihan_id' => $mataPelatihan->id,
            'jenis_pengajar' => 'Widyaiswara',
            'widyaiswara_id' => $wi->id,
        ]);

        // Sinkronkan ke Jadwal SIR-JP
        $syncResponse = $this->actingAs($this->admin)->post(
            route('kelas-mapel.sinkron_jadwal', $kelasMapel->id)
        );
        $syncResponse->assertRedirect();

        $jadwal = Jadwal::where('mata_pelatihan_id', $mataPelatihan->id)->first();
        $this->assertNotNull($jadwal);
        $this->assertEquals(6, $jadwal->jp_rencana);
        $this->assertEquals('Terjadwal', $jadwal->status);

        // =========================================================================
        // TAHAP 3: Buka Sesi Presensi & Dynamic QR Token
        // =========================================================================
        $session = AttendanceSession::create([
            'jadwal_id' => $jadwal->id,
            'kelas_mapel_id' => $kelasMapel->id,
            'judul_sesi' => 'Sesi 1 - Praktik Pemeriksaan K3 ' . $unique,
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '13:30:00',
            'toleransi_terlambat_menit' => 30,
            'status' => 'Aktif',
            'waktu_buka_presensi' => now()->subMinutes(10),
            'qr_secret_key' => 'qr_secret_' . $unique,
            'created_by' => $this->admin->id,
        ]);

        $qrData = $this->attendanceService->generateQrToken($session, 60);
        $this->assertNotEmpty($qrData['payload']);

        // =========================================================================
        // TAHAP 4: Pendaftaran Peserta & Pembuatan Kredensial Portal
        // =========================================================================
        $peserta1 = Peserta::create([
            'nip' => substr('199' . time() . rand(10000000, 99999999), 0, 18),
            'nama' => 'Ahmad Fauzi ' . $unique,
            'email' => 'ahmad' . $unique . '@kemnaker.go.id',
            'jenis_kelamin' => 'Laki-laki',
            'instansi' => 'Disnaker Provinsi DKI Jakarta',
            'jabatan' => 'Pengawas Ketenagakerjaan Muda',
            'angkatan_id' => $angkatan->id,
            'kelas_mapel_id' => $kelasMapel->id,
        ]);
        $accessPeserta1 = $this->attendanceService->getOrGenerateAccessCode($peserta1, '123456');

        $peserta2 = Peserta::create([
            'nip' => substr('199' . (time() + 1) . rand(10000000, 99999999), 0, 18),
            'nama' => 'Siti Nurhaliza ' . $unique,
            'email' => 'siti' . $unique . '@kemnaker.go.id',
            'jenis_kelamin' => 'Perempuan',
            'instansi' => 'Disnaker Kota Bandung',
            'jabatan' => 'Pengawas Ketenagakerjaan Pertama',
            'angkatan_id' => $angkatan->id,
            'kelas_mapel_id' => $kelasMapel->id,
        ]);
        $accessPeserta2 = $this->attendanceService->getOrGenerateAccessCode($peserta2, '654321');

        // =========================================================================
        // TAHAP 5: Peserta 1 Login & Melakukan Presensi Hadir (Direct & QR)
        // =========================================================================
        // Login peserta 1
        $loginRes = $this->post(route('portal.login.post'), [
            'kode_peserta' => $accessPeserta1['kode_peserta'],
            'pin' => '123456',
        ]);
        $loginRes->assertRedirect(route('portal.dashboard'));
        $this->assertEquals($peserta1->id, session('participant_id'));

        // Akses dashboard peserta
        $portalDashRes = $this->withSession(['participant_id' => $peserta1->id])->get(route('portal.dashboard'));
        $portalDashRes->assertStatus(200);
        $portalDashRes->assertSee('Ahmad Fauzi ' . $unique);

        // Peserta 1 melakukan Presensi Hadir Langsung (Direct Attendance)
        $directPresensiRes = $this->withSession(['participant_id' => $peserta1->id])
            ->post(route('portal.presensi.direct.post', $session->id), [
                'status_kehadiran' => 'Hadir',
                'keterangan' => 'Hadir tepat waktu di kelas K3',
            ]);
        $directPresensiRes->assertRedirect();
        $directPresensiRes->assertSessionHas('success');

        // Verifikasi tercatat di database presensi
        $this->assertDatabaseHas('presensis', [
            'attendance_session_id' => $session->id,
            'peserta_id' => $peserta1->id,
            'status_kehadiran' => 'Hadir',
        ]);

        // =========================================================================
        // TAHAP 6: Peserta 2 Mengajukan Izin/Sakit & Verifikasi Persetujuan Admin
        // =========================================================================
        $fakeSuratDokter = UploadedFile::fake()->create('surat_keterangan_sakit.pdf', 250, 'application/pdf');

        $pengajuanRes = $this->withSession(['participant_id' => $peserta2->id])
            ->post(route('portal.pengajuan.post'), [
                'attendance_session_id' => $session->id,
                'jenis_pengajuan' => 'Sakit',
                'alasan' => 'Sakit demam dan flu, istirahat 1 hari sesuai anjuran dokter',
                'bukti_file' => [$fakeSuratDokter],
            ]);
        $pengajuanRes->assertRedirect(route('portal.pengajuan'));

        $req = AttendanceRequest::where('peserta_id', $peserta2->id)
            ->where('attendance_session_id', $session->id)
            ->first();
        $this->assertNotNull($req);
        $this->assertEquals('Pending', $req->status);

        // Admin memeriksa dan menyetujui pengajuan izin
        $verifyRes = $this->actingAs($this->admin)->post(
            route('attendance-requests.verify', $req->id),
            [
                'keputusan' => 'Approved',
                'catatan_admin' => 'Surat keterangan dokter valid. Permohonan disetujui.',
            ]
        );
        $verifyRes->assertRedirect();

        $req->refresh();
        $this->assertEquals('Approved', $req->status);

        // Verifikasi status kehadiran Peserta 2 otomatis menjadi 'Sakit'
        $this->assertDatabaseHas('presensis', [
            'attendance_session_id' => $session->id,
            'peserta_id' => $peserta2->id,
            'status_kehadiran' => 'Sakit',
        ]);

        // =========================================================================
        // TAHAP 7: Peserta 1 Mengerjakan & Mengunggah Tugas Pembelajaran (KCLC Tugas)
        // =========================================================================
        $tugas = Tugas::create([
            'judul' => 'Laporan Hasil Inspeksi K3 Lapangan ' . $unique,
            'deskripsi' => 'Unggah dokumen hasil temuan inspeksi bahaya dan rekomendasi K3.',
            'kategori' => 'individu',
            'penyelenggara' => 'PPSDM Ketenagakerjaan',
            'tanggal_mulai' => now()->startOfDay(),
            'tanggal_selesai' => now()->addDays(2)->endOfDay(),
            'nama_mata_pelatihan' => $mataPelatihan->nama_mata_pelatihan,
        ]);

        $fakeTugasPdf = UploadedFile::fake()->create('laporan_inspeksi_k3.pdf', 300, 'application/pdf');

        $uploadTugasRes = $this->withSession(['participant_id' => $peserta1->id])
            ->post(route('portal.tugas.upload', $tugas->id), [
                'file_jawaban' => $fakeTugasPdf,
                'catatan' => 'Laporan inspeksi K3 telah selesai disusun lengkap dengan matriks risiko.',
            ]);
        $uploadTugasRes->assertRedirect();
        $uploadTugasRes->assertSessionHas('success');

        $this->assertDatabaseHas('pengumpulan_tugas', [
            'tugas_id' => $tugas->id,
            'peserta_id' => $peserta1->id,
            'status' => 'dikumpulkan',
        ]);

        // =========================================================================
        // TAHAP 8: Admin Menutup Sesi Presensi & Mengkonfirmasi Realisasi Jadwal SIR-JP
        // =========================================================================
        $session->update(['status' => 'Selesai']);

        $penugasan = PenugasanPengajar::where('mata_pelatihan_id', $mataPelatihan->id)->first();
        $this->assertNotNull($penugasan);

        $realisasiRes = $this->actingAs($this->admin)->post(route('realisasi.store'), [
            'jadwal_id' => $jadwal->id,
            'tanggal_realisasi' => now()->toDateString(),
            'jam_mulai_aktual' => '08:00',
            'jam_selesai_aktual' => '13:30',
            'status' => 'Terlaksana',
            'total_jp_aktual' => 6,
            'total_jp_dibayarkan' => 6,
            'catatan' => 'Sesi pembelajaran terlaksana penuh dan interaktif.',
            'pengajar' => [
                [
                    'selected' => 1,
                    'penugasan_pengajar_id' => $penugasan->id,
                    'jenis_pengajar' => 'Widyaiswara',
                    'pengajar_id' => $wi->id,
                    'jp_aktual' => 6,
                    'jp_dibayarkan' => 6,
                    'status_kehadiran' => 'Hadir',
                    'keterangan' => 'Mengajar materi K3 tepat waktu.',
                ]
            ],
        ]);
        $realisasiRes->assertRedirect(route('realisasi.index'));

        $jadwal->refresh();
        $this->assertEquals('Terlaksana', $jadwal->status);

        // =========================================================================
        // TAHAP 9: Verifikasi Rekapitulasi SIR-JP & Zero Duplikasi JP
        // =========================================================================
        $rekapDetailRes = $this->actingAs($this->admin)->get(
            route('rekapitulasi.detail', ['id' => $wi->id, 'bulan' => now()->month, 'tahun' => now()->year])
        );
        $rekapDetailRes->assertStatus(200);
        $rekapDetailRes->assertSee($wi->nama_lengkap);
        $rekapDetailRes->assertSee('Teknik Pemeriksaan K3 ' . $unique);

        // =========================================================================
        // TAHAP 10: Pembuatan & Validasi Ekspor Dokumen Laporan Akhir (Excel & PDF)
        // =========================================================================
        // Ekspor Excel Individu WI
        $excelIndividuRes = $this->actingAs($this->admin)->get(
            route('laporan.export.excel_individu', ['id' => $wi->id, 'bulan' => now()->month, 'tahun' => now()->year])
        );
        $excelIndividuRes->assertStatus(200);

        // Ekspor PDF Individu WI
        $pdfIndividuRes = $this->actingAs($this->admin)->get(
            route('laporan.export.pdf_individu', ['id' => $wi->id, 'bulan' => now()->month, 'tahun' => now()->year])
        );
        $pdfIndividuRes->assertStatus(200);

        // Ekspor PDF Rekap Semua WI
        $pdfSemuaRes = $this->actingAs($this->admin)->get(
            route('laporan.export.pdf_semua', ['bulan' => now()->month, 'tahun' => now()->year])
        );
        $pdfSemuaRes->assertStatus(200);

        // =========================================================================
        // TAHAP 11: Verifikasi Integrasi Metrik Dashboard Utama
        // =========================================================================
        $dashRes = $this->actingAs($this->admin)->get(route('dashboard'));
        $dashRes->assertStatus(200);
        $dashRes->assertSee($angkatan->nomor_angkatan);
        $dashRes->assertSee('Dashboard KCLC Learning Center');
    }
}
