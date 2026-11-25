<?php

use App\Services\AttendanceService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('attendance:auto-close', function (AttendanceService $attendanceService) {
    $closed = $attendanceService->autoCloseExpiredSessions();
    $this->info("Sesi presensi kedaluwarsa yang ditutup otomatis: {$closed}");
})->purpose('Menutup otomatis sesi presensi aktif yang melewati tanggal/batas waktu tutup, menandai Alpa, dan menyinkronkan Realisasi JP');

Schedule::command('attendance:auto-close')->hourly();
