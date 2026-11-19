# SIR-JP & KCLC Learning Center (Terintegrasi)
### Sistem Informasi Rekapitulasi Jam Pelajaran & Learning Management System Diklat
**Pusat Pengembangan Sumber Daya Manusia (PPSDM) Kementerian Ketenagakerjaan RI**

---

## 📌 Ringkasan Sistem
Aplikasi web terintegrasi yang menggabungkan:
1. **KCLC (*Kemnaker Corpu Learning Center*)**: Modul pengelolaan pelatihan, angkatan, kelas mata pelatihan (4 tab: Detail, Pengajar, Jadwal, Peserta), ruangan, aspek penilaian sikap, dan form evaluasi.
2. **SIR-JP (*Sistem Informasi Rekapitulasi Jam Pelajaran*)**: Perhitungan otomatis beban kerja mengajar widyaiswara & narasumber dengan batasan minimum **32 JP/bulan**, konversi kegiatan ilmiah/mentoring, serta ekspor Berita Acara & Lampiran Honor (Excel multi-sheet dan PDF).

---

## 🚀 Panduan Menjalankan Aplikasi (Untuk Rekan/Penguji)

### 1. Persyaratan Sistem
* PHP >= 8.2 (ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `gd`, `zip`)
* Composer >= 2.0
* MySQL / MariaDB (disarankan XAMPP atau MySQL Server lokal)

### 2. Langkah Instalasi Cepat
1. **Ekstrak file project** ke folder kerja Anda.
2. **Install Dependensi Composer**:
   ```bash
   composer install
   ```
3. **Konfigurasi Environment (`.env`)**:
   Salin file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   Pastikan pengaturan database di `.env` sesuai dengan MySQL Anda (buat database baru bernama `sir_jp`):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sir_jp
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```
5. **Jalankan Migrasi & Seeder Lengkap**:
   ```bash
   php artisan migrate:fresh --seed
   ```
   *(Perintah ini otomatis mengisi seluruh data pelatihan, widyaiswara, narasumber, riwayat mengajar real Oktober, ruangan, kelas mapel, dan 10 data peserta)*
6. **Jalankan Web Server**:
   ```bash
   php artisan serve
   ```
7. Buka browser di: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 🔐 Akun Login Default
* **Akses**: Halaman Login (`/login`)
* **NIK**: `3171012345670001` *(atau Email: `penyelenggara@sir-jp.go.id`)*
* **Password**: `password123`
* **Role**: Penyelenggara Diklat (Admin Tunggal)

---

## 📂 Berkas yang Perlu Dikirim ke Pihak Lain

### Skenario A: Jika Dikirim ke Pimpinan / Penguji / Klien (Review Hasil)
1. **Akses Demo**: Kirimkan URL aplikasi serta NIK & Password login di atas.
2. **File Laporan Rekapitulasi Excel & PDF**:
   - Dapat diunduh langsung dari menu **Rekapitulasi JP &rarr; Export Excel / PDF** (Format Berita Acara resmi & Lampiran Honor Widyaiswara).
3. **Dokumen Panduan Singkat (README ini)**.

### Skenario B: Jika Dikirim ke Tim IT / Programmer (Source Code)
1. **Source Code (.ZIP)**:
   > ⚠️ **Catatan Penting**: Sebelum di-ZIP, **HAPUS / JANGAN SERTAKAN** folder:
   > - `vendor/` *(ukurannya besar, penerima cukup ketik `composer install`)*
   > - `node_modules/` *(jika ada)*
   > - `.git/` *(opsional)*
   > - File `.env` *(biarkan penerima menggunakan `.env.example`)*
2. **README.md** (instruksi instalasi di atas).

---

## 🧪 Verifikasi & Pengujian Otomatis
Sistem ini dilengkapi 33 automated test suite dengan 192 assertion tanpa error:
```bash
php artisan test
```
* **EndToEndInterconnectedFlowTest**: Menguji alur lengkap dari KCLC Ruangan &rarr; Kelas Mapel &rarr; Sesi Jadwal &rarr; Peserta & Presensi &rarr; Realisasi Mengajar &rarr; Rekapitulasi JP &rarr; Export Excel/PDF.
* **KCLCIntegrationTest**: Menguji 4 tab kelas mapel, form sikap & evaluasi dinamis, dan dashboard.
* **OktoberExcelFormatVerificationTest**: Memverifikasi formula perhitungan dan format multi-sheet.
