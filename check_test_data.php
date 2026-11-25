<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "MataPelatihan DIR: " . App\Models\MataPelatihan::where('nama_mata_pelatihan', 'like', '%DIR%')->count() . "\n";
echo "Angkatan ATT: " . App\Models\Angkatan::where('nomor_angkatan', 'like', '%ATT%')->count() . "\n";
echo "KelasMapel ATT: " . App\Models\KelasMapel::where('nama_kelas', 'like', '%ATT%')->count() . "\n";
echo "Users test: " . App\Models\User::where('email', 'like', '%@test.com')->orWhere('email', 'like', '%test_%')->count() . "\n";
echo "Attendance Sessions: " . App\Models\AttendanceSession::count() . "\n";
