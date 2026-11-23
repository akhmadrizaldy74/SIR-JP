<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WidyaiswaraController;
use App\Http\Controllers\NarasumberController;
use App\Http\Controllers\PelatihanController;
use App\Http\Controllers\AngkatanController;
use App\Http\Controllers\MataPelatihanController;
use App\Http\Controllers\PenugasanPengajarController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\RealisasiController;
use App\Http\Controllers\KonversiJpController;
use App\Http\Controllers\RekapitulasiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\KelasMapelController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\AspekPenilaianSikapController;
use App\Http\Controllers\FormEvaluasiController;
use App\Http\Controllers\ParticipantAuthController;
use App\Http\Controllers\ParticipantPortalController;
use App\Http\Controllers\ParticipantTugasController;
use App\Http\Controllers\AttendanceSessionController;
use App\Http\Controllers\AttendanceManualController;
use App\Http\Controllers\AttendanceRequestController;
use App\Http\Controllers\ClassDocumentationController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\ParticipantAccessController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MoocController;
use App\Http\Controllers\JenisPelatihanController;
use App\Http\Controllers\TahunKurikulumController;

/*
|--------------------------------------------------------------------------
| Web Routes - SIR-JP (Sistem Informasi Rekapitulasi Jam Pelajaran)
|--------------------------------------------------------------------------
*/

// Authentication (Admin Penyelenggara)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:15,1')->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==============================================================
// PORTAL PRESENSI PESERTA DIKLAT (SEPARATE & MOBILE-OPTIMIZED)
// ==============================================================
Route::get('/presensi/login', [ParticipantAuthController::class, 'showLoginForm'])->name('portal.login');
Route::post('/presensi/login', [ParticipantAuthController::class, 'login'])->middleware('throttle:15,1')->name('portal.login.post');
Route::post('/presensi/logout', [ParticipantAuthController::class, 'logout'])->name('portal.logout');

Route::middleware(\App\Http\Middleware\EnsureParticipantAuth::class)->prefix('presensi')->name('portal.')->group(function () {
    Route::get('/', [ParticipantPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/scan', [ParticipantPortalController::class, 'scan'])->name('scan');
    Route::post('/scan', [ParticipantPortalController::class, 'submitScan'])->name('scan.post');
    Route::get('/riwayat', [ParticipantPortalController::class, 'riwayat'])->name('riwayat');
    Route::get('/pengajuan', [ParticipantPortalController::class, 'pengajuan'])->name('pengajuan');
    Route::post('/pengajuan', [ParticipantPortalController::class, 'submitPengajuan'])->name('pengajuan.post');
    Route::post('/presensi-mandiri/{sessionId}', [ParticipantPortalController::class, 'submitDirectPresensi'])->name('presensi.direct.post');

    // Modul Tugas Peserta
    Route::get('/tugas', [ParticipantTugasController::class, 'index'])->name('tugas.index');
    Route::post('/tugas/{id}/upload', [ParticipantTugasController::class, 'upload'])->name('tugas.upload');
    Route::get('/tugas/{id}/download', [ParticipantTugasController::class, 'download'])->name('tugas.download');
});

// Protected Routes (Penyelenggara)
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // 1. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Data Widyaiswara
    Route::resource('widyaiswara', WidyaiswaraController::class);

    // 3. Data Narasumber (Tabel terpisah dari Widyaiswara)
    Route::resource('narasumber', NarasumberController::class);

    // 4. Data Pelatihan & Kurikulum KCLC
    Route::resource('jenis-pelatihan', JenisPelatihanController::class);
    Route::patch('jenis-pelatihan/{jenis_pelatihan}/toggle', [JenisPelatihanController::class, 'toggle'])->name('jenis-pelatihan.toggle');
    Route::resource('pelatihan', PelatihanController::class);
    Route::patch('pelatihan/{pelatihan}/toggle', [PelatihanController::class, 'toggle'])->name('pelatihan.toggle');
    Route::resource('tahun-kurikulum', TahunKurikulumController::class);

    // 5. Data Angkatan
    Route::resource('angkatan', AngkatanController::class);

    // KCLC LMS Diklat Modules (Terintegrasi)
    Route::resource('ruangan', RuanganController::class);
    Route::resource('kelas-mapel', KelasMapelController::class);
    Route::post('kelas-mapel/{kelas_mapel}/pengajar', [KelasMapelController::class, 'storePengajar'])->name('kelas-mapel.pengajar.store');
    Route::delete('kelas-mapel/{kelas_mapel}/pengajar/{pengajar}', [KelasMapelController::class, 'destroyPengajar'])->name('kelas-mapel.pengajar.destroy');
    Route::post('kelas-mapel/{kelas_mapel}/sinkron-jadwal', [KelasMapelController::class, 'sinkronJadwal'])->name('kelas-mapel.sinkron_jadwal');
    Route::resource('peserta', PesertaController::class);
    Route::resource('sikap-form', AspekPenilaianSikapController::class);
    Route::patch('sikap-form/{sikap_form}/toggle', [AspekPenilaianSikapController::class, 'toggle'])->name('sikap-form.toggle');
    Route::resource('evaluasi-form', FormEvaluasiController::class);

    // ==============================================================
    // MODUL PRESENSI HYBRID KCLC (SESI, QR, MANUAL, IZIN, DOKUMENTASI)
    // ==============================================================
    Route::prefix('presensi-admin')->name('presensi.')->group(function () {
        Route::get('/dashboard', [AttendanceReportController::class, 'index'])->name('dashboard');
        Route::get('/rekap', [AttendanceReportController::class, 'rekap'])->name('rekap');
        Route::get('/export/excel', [AttendanceReportController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [AttendanceReportController::class, 'exportPdf'])->name('export.pdf');
    });

    // Sesi Presensi & QR Code
    Route::resource('attendance-sessions', AttendanceSessionController::class);
    Route::post('attendance-sessions/{attendance_session}/activate', [AttendanceSessionController::class, 'activate'])->name('attendance-sessions.activate');
    Route::post('attendance-sessions/{attendance_session}/close', [AttendanceSessionController::class, 'close'])->name('attendance-sessions.close');
    Route::get('attendance-sessions/{attendance_session}/qr-display', [AttendanceSessionController::class, 'qrDisplay'])->name('attendance-sessions.qr_display');
    Route::get('attendance-sessions/{attendance_session}/qr-token', [AttendanceSessionController::class, 'getLatestQrToken'])->name('attendance-sessions.qr_token');

    // Presensi Manual Admin
    Route::get('attendance-sessions/{attendance_session}/manual', [AttendanceManualController::class, 'index'])->name('attendance-manual.index');
    Route::post('attendance-sessions/{attendance_session}/manual', [AttendanceManualController::class, 'updateSingle'])->name('attendance-manual.update');
    Route::post('attendance-sessions/{attendance_session}/manual-bulk', [AttendanceManualController::class, 'bulkUpdate'])->name('attendance-manual.bulk');
    Route::get('attendance-sessions/{attendance_session}/logs/{peserta}', [AttendanceManualController::class, 'getLogs'])->name('attendance-manual.logs');

    // Pengajuan Izin / Sakit / Dispensasi & Bukti
    Route::get('attendance-requests', [AttendanceRequestController::class, 'index'])->name('attendance-requests.index');
    Route::post('attendance-requests/{attendance_request}/verify', [AttendanceRequestController::class, 'verify'])->name('attendance-requests.verify');
    Route::get('attendance-evidences/{evidence}', [AttendanceRequestController::class, 'downloadEvidence'])->name('attendance-evidences.download');

    // Dokumentasi Foto Kegiatan
    Route::get('class-documentations', [ClassDocumentationController::class, 'index'])->name('class-documentations.index');
    Route::post('class-documentations', [ClassDocumentationController::class, 'store'])->name('class-documentations.store');
    Route::get('class-documentations/{class_documentation}/view', [ClassDocumentationController::class, 'viewFile'])->name('class-documentations.view');
    Route::delete('class-documentations/{class_documentation}', [ClassDocumentationController::class, 'destroy'])->name('class-documentations.destroy');

    // Manajemen Kode Akses & PIN Peserta
    Route::get('participant-access-codes', [ParticipantAccessController::class, 'index'])->name('participant-access.index');
    Route::post('participant-access-codes/massal', [ParticipantAccessController::class, 'generateMassal'])->name('participant-access.massal');
    Route::post('participant-access-codes/{peserta}/reset', [ParticipantAccessController::class, 'resetPin'])->name('participant-access.reset');
    Route::post('participant-access-codes/modules', [ParticipantAccessController::class, 'updateModules'])->name('participant-access.modules');

    // 6. Mata Pelatihan
    Route::resource('mata-pelatihan', MataPelatihanController::class);

    // 7. Penugasan Pengajar
    Route::resource('penugasan', PenugasanPengajarController::class);

    // 8. Jadwal Pelatihan
    Route::resource('jadwal', JadwalController::class);

    // 9. Realisasi Kegiatan
    Route::resource('realisasi', RealisasiController::class);

    // 10. Konversi JP
    Route::resource('konversi', KonversiJpController::class);

    // 11. Rekapitulasi JP
    Route::prefix('rekapitulasi')->name('rekapitulasi.')->group(function () {
        Route::get('/', [RekapitulasiController::class, 'index'])->name('index');
        Route::get('/detail/{id}', [RekapitulasiController::class, 'detail'])->name('detail');
        Route::get('/detail-narasumber/{id}', [RekapitulasiController::class, 'detailNarasumber'])->name('detail_narasumber');
        Route::get('/tahunan', [RekapitulasiController::class, 'tahunan'])->name('tahunan');
    });

    // 12. Laporan & Ekspor
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');

        // Ekspor Excel
        Route::get('/export/excel-individu/{id}', [LaporanController::class, 'exportExcelIndividu'])->name('export.excel_individu');
        Route::get('/export/excel-semua', [LaporanController::class, 'exportExcelSemua'])->name('export.excel_semua');
        Route::get('/export/excel-tahunan', [LaporanController::class, 'exportExcelTahunan'])->name('export.excel_tahunan');
        Route::get('/export/excel-pelatihan', [LaporanController::class, 'exportExcelPelatihan'])->name('export.excel_pelatihan');

        // Ekspor PDF
        Route::get('/export/pdf-individu/{id}', [LaporanController::class, 'exportPdfIndividu'])->name('export.pdf_individu');
        Route::get('/export/pdf-semua', [LaporanController::class, 'exportPdfSemua'])->name('export.pdf_semua');
        Route::get('/export/pdf-tahunan', [LaporanController::class, 'exportPdfTahunan'])->name('export.pdf_tahunan');
        Route::get('/export/pdf-narasumber/{id}', [LaporanController::class, 'exportPdfNarasumber'])->name('export.pdf_narasumber');
    });

    // 13. Pengaturan
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::get('/', [PengaturanController::class, 'index'])->name('index');
        Route::put('/umum', [PengaturanController::class, 'updateUmum'])->name('update.umum');
        Route::put('/sesi/{sesi}', [PengaturanController::class, 'updateSesi'])->name('update.sesi');
    });

    // Profil & Password
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/password', [ProfileController::class, 'password'])->name('password');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    // 14. Manajemen Pengguna Sistem & Peran (Role)
    Route::resource('users', UserController::class);

    // 15. LMS MOOC (Modul Pengajar & Administrator)
    Route::prefix('mooc')->name('mooc.')->group(function () {
        Route::get('/pelatihan', [MoocController::class, 'pelatihan'])->name('pelatihan');
        Route::get('/bank-soal', [MoocController::class, 'bankSoal'])->name('bank-soal');
        Route::get('/riwayat', [MoocController::class, 'riwayat'])->name('riwayat');
        Route::get('/komentar', [MoocController::class, 'komentar'])->name('komentar');
        Route::get('/nilai-ujian', [MoocController::class, 'nilaiUjian'])->name('nilai-ujian');
    });

    // 16. Ganti Mode Peran / Swipe Role Dinamis Sesuai KCLC
    Route::get('/switch-role/{role}', [AuthController::class, 'switchRole'])->name('switch-role');

    // JSON API Endpoints untuk Interaktivitas Dinamis Form
    Route::get('/api/angkatans-by-pelatihan/{pelatihanId}', [PenugasanPengajarController::class, 'getAngkatansByPelatihan']);
    Route::get('/api/mata-pelatihans-by-angkatan/{angkatanId}', [PenugasanPengajarController::class, 'getMataPelatihansByAngkatan']);
    Route::get('/api/pengajars-by-mata-pelatihan/{mataPelatihanId}', [JadwalController::class, 'getPengajarsByMataPelatihan']);
    Route::post('/api/pengajar-quick-store', [JadwalController::class, 'quickStorePengajar'])->name('api.pengajar.quick_store');
});
