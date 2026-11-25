# LAPORAN AUDIT KEAMANAN SISTEM (SECURITY AUDIT)
## SIR-JP & KCLC Learning Center - PPSDM Kementerian Ketenagakerjaan RI
**Tanggal Audit:** 23 November 2026  
**Auditor:** Senior Security Engineer & Laravel Developer  
**Status Audit:** Tahap 1 - Audit Selesai (Menunggu Persetujuan Rencana Perbaikan)

---

## 1. RINGKASAN EKSEKUTIF
Audit keamanan komprehensif telah dilakukan terhadap seluruh komponen aplikasi **SIR-JP & KCLC** yang mencakup:
- Routing dan Middleware (`routes/web.php`, `bootstrap/app.php`)
- Controllers & Form Request (`app/Http/Controllers/`)
- Models & Database Migrations (`app/Models/`, `database/migrations/`, `database/seeders/`)
- Export Modules (`app/Exports/`, `app/Services/`)
- Views & Blade Templates (`resources/views/`)
- Konfigurasi Lingkungan (`config/`, `.env.example`, `.gitignore`)
- Riwayat Git & Audit Dependensi (`composer audit`, `npm audit`)

Semua 33 unit dan feature automated test suite saat ini dalam kondisi **LULUS (192 assertions)**. Tidak ditemukan kerentanan pada dependensi third-party (`composer audit` & `npm audit` 0 vulnerability). Seluruh model Eloquent telah menggunakan `$fillable` eksplisit (tidak ada mass assignment `$guarded = []`), dan seluruh form telah memiliki `@csrf`.

Namun demikian, audit menemukan **14 temuan keamanan** yang perlu ditingkatkan, diklasifikasikan ke dalam 4 tingkatan risiko:
- **Kritis (Critical):** 1 temuan
- **Tinggi (High):** 5 temuan
- **Sedang (Medium):** 7 temuan
- **Rendah (Low):** 1 temuan

---

## 2. DAFTAR TEMUAN BERDASARKAN TINGKAT RISIKO

### 🔴 TINGKAT RISIKO: KRITIS (CRITICAL)

#### SEC-01: Kebijakan Rate Limiting Login Perlu Ditinjau
* **File Terkait:** `routes/web.php` (route login dan portal peserta)
* **Kategori:** B. Autentikasi
* **Penjelasan Singkat:**  
  Endpoint `POST /login` dan login portal peserta saat ini menggunakan middleware `throttle:15,1`. Karena sudah ada pembatasan laju, temuan sebelumnya yang menyatakan throttle tidak ada sudah tidak berlaku. Kebijakan ini tetap perlu ditinjau untuk memastikan batasnya sesuai risiko dan mempertimbangkan pembatasan berbasis kombinasi IP dan identitas login.
* **Rencana Perbaikan:**  
  1. Evaluasi apakah batas `15` percobaan per menit sudah sesuai kebijakan autentikasi.
  2. Jika diperlukan, tambahkan pembatasan per identitas login selain throttle berbasis route.

---

### 🟠 TINGKAT RISIKO: TINGGI (HIGH)

#### SEC-02: Kerentanan Spreadsheet Formula Injection (CSV / Excel DDE Injection)
* **File Terkait:**  
  - `app/Exports/PelatihanExport.php` & `resources/views/exports/pelatihan_excel.blade.php`
  - `app/Exports/RekapSummarySheetExport.php` (baris 130-170)
  - `app/Exports/WidyaiswaraIndividuExport.php` (baris 180-250)
* **Kategori:** C. Injeksi dan Input
* **Penjelasan Singkat:**  
  Data input dinamis (nama widyaiswara, nama pelatihan, nama kegiatan konversi, keterangan) dicetak langsung ke dalam sel Excel. Jika pengguna memasukkan karakter awal berupa `=`, `+`, `-`, `@`, `\t`, atau `\r`, spreadsheet reader (Microsoft Excel atau LibreOffice) akan mengeksekusinya sebagai formula otomatis atau Dynamic Data Exchange (DDE) ketika file diunduh oleh staf atau pimpinan.
* **Rencana Perbaikan:**  
  Buat fungsi sanitasi nilai sel (`sanitizeFormulaInjection`) yang menambahkan tanda petik tunggal (`'`) di depan teks jika karakter pertama diawali karakter formula sebelum ditulis ke lembar kerja Excel.

#### SEC-03: Kredensial Default Terbuka pada DatabaseSeeder di Lingkungan Production
* **File Terkait:** `database/seeders/DatabaseSeeder.php` (baris 28-44)
* **Kategori:** B. Autentikasi
* **Penjelasan Singkat:**  
  `DatabaseSeeder` secara otomatis men-seed akun admin `penyelenggara@sir-jp.go.id` dengan kata sandi hardcoded `password123` tanpa memvalidasi apakah aplikasi berada pada environment `local` atau `testing`. Jika seeder dijalankan saat inisialisasi server production, kredensial ini aktif dan dapat disusupi pihak luar.
* **Rencana Perbaikan:**  
  Bungkus pembuatan akun default dengan kondisi `if (app()->environment('local', 'testing'))`. Buat perintah Artisan khusus (`php artisan sirjp:create-admin`) dengan prompt interaktif yang aman untuk inisialisasi akun penyelenggara di server production.

#### SEC-04: Aturan Kompleksitas Kata Sandi Terlalu Lemah
* **File Terkait:**  
  - `app/Http/Controllers/AuthController.php` (baris 90)
  - `app/Http/Controllers/ProfileController.php` (baris 54)
* **Kategori:** B. Autentikasi
* **Penjelasan Singkat:**  
  Validasi password saat pendaftaran dan pergantian password hanya mensyaratkan `'min:6'` tanpa keharusan menyertakan huruf besar, huruf kecil, angka, atau simbol. Kata sandi 6 karakter sangat rentan diretas melalui serangan kamus (dictionary attack).
* **Rencana Perbaikan:**  
  Tingkatkan validasi password menggunakan standar Laravel:  
  `Password::min(12)->letters()->mixedCase()->numbers()->symbols()->uncompromised()`

#### SEC-05: Kredensial Default dan Data Sensitif Tercantum di README.md
* **File Terkait:** `README.md` (baris 62-67)
* **Kategori:** G. Dokumentasi & E. Konfigurasi
* **Penjelasan Singkat:**  
  Dokumen `README.md` mencantumkan NIK akun default `3171012345670001` dan password `password123` secara gamblang. Berkas ini berpotensi terekspos jika repositori dibagikan atau dipublikasikan.
* **Rencana Perbaikan:**  
  Hapus kredensial default dari `README.md` dan gantikan dengan panduan aman membuat akun penyelenggara pertama kali melalui Artisan command.

#### SEC-06: Penyimpanan NIK (Nomor Induk Kependudukan) dalam Bentuk Plaintext Tanpa Masking di Log
* **File Terkait:**  
  - `app/Models/User.php`
  - `app/Models/Widyaiswara.php`
  - `app/Models/Narasumber.php`
  - `app/Http/Controllers/AuthController.php` (baris 61, 67, 124)
* **Kategori:** D. Data Sensitif
* **Penjelasan Singkat:**  
  NIK merupakan data pribadi sensitif (UU PDP). Saat ini NIK disimpan sebagai teks biasa, dan pada proses registrasi serta login gagal/berhasil, NIK (atau input teks user yang bisa jadi tidak sengaja berupa password) dicatat apa adanya ke tabel `log_aktivitas`.
* **Rencana Perbaikan:**  
  1. Bersihkan log aktivitas: lakukan masking pada NIK di log (contoh: `317101******0001`), dan jangan pernah mencatat string login mentah pada event login gagal untuk mencegah password yang salah ketik masuk ke database log.
  2. Menambahkan NIK ke properti `$hidden` di model `User` agar tidak otomatis ter-serialize pada respon JSON.
  3. Menyediakan rekomendasi enkripsi data at-rest (TDE atau hashing deterministik untuk lookup NIK).

---

### 🟡 TINGKAT RISIKO: SEDANG (MEDIUM)

#### SEC-07: Ketiadaan Middleware Otorisasi Role & Gate Pengecekan Akses
* **File Terkait:** `routes/web.php` (baris 39-130)
* **Kategori:** A. Otorisasi dan Akses
* **Penjelasan Singkat:**  
  Seluruh endpoint hanya menggunakan middleware `auth` standar. Walaupun saat ini pengguna aplikasi adalah penyelenggara, tidak ada middleware pengecekan status aktif akun secara realtime pada setiap request (`auth.active`) maupun otorisasi role (`role:penyelenggara`). Jika akun dinonaktifkan di database saat sesi masih berjalan, pengguna tetap bisa beraktivitas hingga sesi kedaluwarsa.
* **Rencana Perbaikan:**  
  Tambahkan middleware `EnsureAccountIsActiveAndAuthorized` yang memverifikasi bahwa `auth()->user()->status === 'Aktif'` dan memiliki wewenang pada setiap request.

#### SEC-08: Ketiadaan Security Headers HTTP (HSTS, CSP, X-Frame-Options, dll.)
* **File Terkait:** `bootstrap/app.php` (baris 13-15)
* **Kategori:** E. Konfigurasi
* **Penjelasan Singkat:**  
  Aplikasi belum menyertakan header keamanan HTTP standar, sehingga rentan terhadap serangan Clickjacking, MIME sniffing, dan eksploitasi konten pihak ketiga.
* **Rencana Perbaikan:**  
  Tambahkan middleware `SecurityHeaders` ke pipeline global di `bootstrap/app.php` dengan header:
  - `X-Frame-Options: SAMEORIGIN`
  - `X-Content-Type-Options: nosniff`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Strict-Transport-Security: max-age=31536000; includeSubDomains`
  - `Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://cdn.jsdelivr.net https://fonts.gstatic.com data:; img-src 'self' data:; connect-src 'self';`

#### SEC-09: Nilai Default Cookie Session `secure` Belum Bernilai `true`
* **File Terkait:** `config/session.php` (baris 172)
* **Kategori:** E. Konfigurasi
* **Penjelasan Singkat:**  
  Opsi `'secure' => env('SESSION_SECURE_COOKIE')` bernilai null secara bawaan. Bila variabel ini tidak diatur secara eksplisit di server produksi, cookie session dapat dikirimkan melalui koneksi HTTP tidak terenkripsi (rentan serangan man-in-the-middle).
* **Rencana Perbaikan:**  
  Atur default `'secure' => env('SESSION_SECURE_COOKIE', true)` di konfigurasi produksi dan pastikan terdokumentasi di `.env.example`.

#### SEC-10: Ketiadaan Session Regeneration pada Alur Registrasi Akun
* **File Terkait:** `app/Http/Controllers/AuthController.php` (baris 118-123)
* **Kategori:** B. Autentikasi
* **Penjelasan Singkat:**  
  Saat akun penyelenggara baru didaftarkan dan langsung di-login-kan via `Auth::login($user)`, tidak dilakukan `$request->session()->regenerate()`. Hal ini berisiko terhadap *Session Fixation*.
* **Rencana Perbaikan:**  
  Panggil `$request->session()->regenerate();` setelah pendaftaran berhasil sebelum dialihkan ke dashboard.

#### SEC-11: Validasi Masih Tersebar di Controller (Belum Form Request Terpusat)
* **File Terkait:** Seluruh Controller di `app/Http/Controllers/`
* **Kategori:** C. Injeksi dan Input
* **Penjelasan Singkat:**  
  Validasi input masih dilakukan dengan `$request->validate()` langsung di dalam controller. Tidak ada filter pembersihan tag HTML (strip tags) eksplisit pada input teks bebas sehingga berpotensi lolos data yang tidak steril.
* **Rencana Perbaikan:**  
  Buat Form Request khusus untuk entitas penting (misal: `StoreWidyaiswaraRequest`, `StorePelatihanRequest`, `UpdateProfileRequest`) yang menerapkan pembersihan input otomatis (sanitasi string, trim, strip tags) dan penegasan otorisasi.

#### SEC-12: Ketiadaan Audit Trail (LogAktivitas) pada Seluruh Modul KCLC
* **File Terkait:**  
  - `app/Http/Controllers/PesertaController.php`
  - `app/Http/Controllers/RuanganController.php`
  - `app/Http/Controllers/KelasMapelController.php`
  - `app/Http/Controllers/AspekPenilaianSikapController.php`
  - `app/Http/Controllers/FormEvaluasiController.php`
* **Kategori:** F. Audit Log
* **Penjelasan Singkat:**  
  Operasi penambahan, pengubahan, dan penghapusan data pada modul KCLC (peserta diklat, ruangan, kelas mapel, pengajar mapel, aspek sikap, dan form evaluasi) belum mencatat log audit ke tabel `log_aktivitas`. Perubahan data vital diklat tidak memiliki jejak rekam digital (siapa, apa, kapan).
* **Rencana Perbaikan:**  
  Sematkan panggilan `LogAktivitas::catat(...)` pada seluruh operasi mutasi data (store, update, destroy, toggle) di modul KCLC.

#### SEC-13: Ketiadaan Rate Limiter pada Endpoint Ekspor Laporan (Excel & PDF)
* **File Terkait:** `routes/web.php` (baris 94-108)
* **Kategori:** A. Otorisasi dan Akses
* **Penjelasan Singkat:**  
  Endpoint generate PDF dan Excel multi-sheet memakan CPU dan memori server yang signifikan. Ketiadaan rate limiter memungkinkan terjadinya kehabisan memori server (*Denial of Service*) jika endpoint diakses secara beruntun dalam waktu singkat.
* **Rencana Perbaikan:**  
  Terapkan middleware `throttle:15,1` (maksimal 15 permintaan ekspor per menit per pengguna) pada grup route laporan ekspor.

---

### 🟢 TINGKAT RISIKO: RENDAH (LOW)

#### SEC-14: Ketiadaan Panduan Deployment Produksi Aman
* **File Terkait:** Repositori (Belum ada berkas panduan deploy)
* **Kategori:** G. Dokumentasi
* **Penjelasan Singkat:**  
  Belum tersedia panduan tertulis standar hardening server untuk tim infrastruktur/DevOps (pengaturan DocumentRoot ke `/public`, proteksi folder `.git`, hak akses chmod storage, HTTPS, backup berkala, dan user DB non-root).
* **Rencana Perbaikan:**  
  Susun berkas panduan deployment komprehensif `SECURITY_CHECKLIST_DEPLOY.md`.

---

## 3. USULAN PENERAPAN TWO-FACTOR AUTHENTICATION (2FA) UNTUK ADMIN
Sebagai perlindungan ekstra terhadap akun Penyelenggara (yang mengelola nilai honor, rekapitulasi jam pelajaran, dan data identitas widyaiswara):
* **Konsep:** Penggunaan Time-based One-Time Password (TOTP) berbasis RFC 6238 yang kompatibel dengan aplikasi umum seperti Google Authenticator, Microsoft Authenticator, atau FreeOTP.
* **Alur:**
  1. Penyelenggara membuka menu *Profil & Keamanan*.
  2. Pengguna mengaktifkan 2FA, sistem menghasilkan *secret key* unik dan menampilkan QR Code untuk dipindai.
  3. Pengguna memverifikasi kode 6 digit pertama dan menyimpan 8 kode cadangan (Recovery Codes).
  4. Setiap login berikutnya, setelah memasukkan NIK dan password, pengguna diminta memasukkan kode 6 digit dari aplikasi authenticator.
* **Status:** Diusulkan sebagai peningkatan fase berikutnya (memerlukan persetujuan pengguna sebelum modul 2FA diinstal).

---

## 4. RENCANA KERJA PERBAIKAN (ROADMAP REMEDIASI)

Perbaikan akan dilaksanakan secara bertahap per kategori setelah disetujui, dengan pengujian otomatis (`php artisan test`) di setiap akhir kategori untuk memastikan 33 test dasar tetap lulus 100%:

| Urutan | Kategori | Fokus Perbaikan |
|---|---|---|
| **Tahap 1** | **B. Autentikasi** | Rate limiting login (`throttle`), aturan password kuat (`Password::min(12)`), regenerasi session di register, proteksi seeder production & artisan command create admin. |
| **Tahap 2** | **C. Injeksi & Input** | Sanitasi formula injection pada seluruh ekspor Excel, pembuatan Form Request tersentralisasi dengan sanitasi string. |
| **Tahap 3** | **E. Konfigurasi & Header** | Security Headers middleware (CSP, X-Frame, HSTS, dll.), default session cookie secure, perbaikan `.env.example`. |
| **Tahap 4** | **A. Otorisasi & Akses** | Middleware pengecekan status user aktif, rate limiting pada endpoint ekspor laporan, kebijakan akses. |
| **Tahap 5** | **D. Data Sensitif & F. Audit Log** | Masking NIK dan sanitasi input pada `log_aktivitas`, `$hidden` NIK di model User, penambahan audit log menyeluruh pada modul KCLC. |
| **Tahap 6** | **G. Dokumentasi & Finalisasi** | Pembuatan `SECURITY_CHECKLIST_DEPLOY.md`, pembersihan kredensial di `README.md`, dan penulisan automated test keamanan baru. |

---
**Catatan:** Mohon konfirmasi atau persetujuan untuk memulai eksekusi perbaikan dimulai dari **Tahap 1 (Autentikasi)**.
