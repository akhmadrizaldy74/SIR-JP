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

/*
|--------------------------------------------------------------------------
| Web Routes - SIR-JP (Sistem Informasi Rekapitulasi Jam Pelajaran)
|--------------------------------------------------------------------------
*/

// Authentication
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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

    // 4. Data Pelatihan
    Route::resource('pelatihan', PelatihanController::class);

    // 5. Data Angkatan
    Route::resource('angkatan', AngkatanController::class);

    // KCLC LMS Diklat Modules (Terintegrasi)
    Route::resource('ruangan', RuanganController::class);
    Route::resource('kelas-mapel', KelasMapelController::class);
    Route::post('kelas-mapel/{kelas_mapel}/pengajar', [KelasMapelController::class, 'storePengajar'])->name('kelas-mapel.pengajar.store');
    Route::delete('kelas-mapel/{kelas_mapel}/pengajar/{pengajar}', [KelasMapelController::class, 'destroyPengajar'])->name('kelas-mapel.pengajar.destroy');
    Route::resource('peserta', PesertaController::class);
    Route::resource('sikap-form', AspekPenilaianSikapController::class);
    Route::patch('sikap-form/{sikap_form}/toggle', [AspekPenilaianSikapController::class, 'toggle'])->name('sikap-form.toggle');
    Route::resource('evaluasi-form', FormEvaluasiController::class);

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

    // JSON API Endpoints untuk Interaktivitas Dinamis Form
    Route::get('/api/angkatans-by-pelatihan/{pelatihanId}', [PenugasanPengajarController::class, 'getAngkatansByPelatihan']);
    Route::get('/api/mata-pelatihans-by-angkatan/{angkatanId}', [PenugasanPengajarController::class, 'getMataPelatihansByAngkatan']);
    Route::get('/api/pengajars-by-mata-pelatihan/{mataPelatihanId}', [JadwalController::class, 'getPengajarsByMataPelatihan']);
    Route::post('/api/pengajar-quick-store', [JadwalController::class, 'quickStorePengajar'])->name('api.pengajar.quick_store');
});
