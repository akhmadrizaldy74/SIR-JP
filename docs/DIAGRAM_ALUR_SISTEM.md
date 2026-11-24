# DIAGRAM ALUR SISTEM KCLC & SIR-JP (KEMNAKER CORPU)

Dokumentasi diagram alur lengkap terpadu yang dipisah menjadi 5 modul utama menggunakan sintaks Mermaid.

---

## 1. Modul Kurikulum & Master Data (Tahap Awal)

```mermaid
flowchart TD
    subgraph Master_Kurikulum ["1. Modul Kurikulum (KCLC)"]
        A["Mulai: Menu Kurikulum"] --> B["Jenis Pelatihan<br/>(Teknis / Fungsional / Manajerial)"]
        A --> C["Tahun Kurikulum<br/>(Contoh: 2026)"]
        B & C --> D["Data Pelatihan & Angkatan<br/>(Kode & Nama Pelatihan)"]
        D --> E["Mata Pelatihan<br/>(Alokasi Rencana JP)"]
    end

    subgraph Relasi_Terkait ["Keterkaitan Modul"]
        E -->|"Relasi ID Program & JP"| F["Data Kelas Mapel<br/>(KCLC)"]
        E -->|"Dasar Rekapitulasi Rencana"| G["Master Penugasan<br/>(SIR-JP)"]
        H["Master Ruangan<br/>(Kapasitas & Lokasi)"] -->|"Penetapan Lokasi"| F
    end

    classDef startNode fill:#0B1727,stroke:#0284C7,stroke-width:2px,color:#fff;
    classDef processNode fill:#F0FDF4,stroke:#16A34A,stroke-width:1.5px,color:#064E3B;
    classDef relasiNode fill:#EFF6FF,stroke:#2563EB,stroke-width:1.5px,color:#1E3A8A;

    class A startNode;
    class B,C,D,E processNode;
    class F,G,H relasiNode;
```

---

## 2. Modul Data Kelas (KCLC) & Sinkronisasi SIR-JP

```mermaid
flowchart TD
    subgraph Data_Kelas ["2. Modul Data Kelas KCLC (4 Tab)"]
        K["Buka Detail Kelas Mapel"] --> T1["Tab 1: Informasi Kelas<br/>(Metode, Tanggal & Jam)"]
        K --> T2["Tab 2: Penugasan Pengajar<br/>(Pilih WI / Narasumber & JP)"]
        K --> T3["Tab 3: Sesi & Jadwal<br/>(Penyusunan Rencana Sesi)"]
        K --> T4["Tab 4: Peserta Diklat<br/>(Data NIK/NIP & Instansi)"]
    end

    subgraph Auto_Sync ["Otomatis Terhubung Ke"]
        T2 -->|"Auto-Sync Penugasan"| S1["Penugasan Pengajar<br/>(Basis Data SIR-JP)"]
        T3 -->|"Tombol Sinkron Jadwal"| S2["Jadwal Belajar SIR-JP<br/>(Status: Terjadwal)"]
        T3 -->|"Buka Sesi Kelas"| S3["Sesi Presensi Aktif<br/>(Kode Token QR Dinamis)"]
        T4 -->|"Generate Access Code"| S4["Kredensial Portal Peserta<br/>(Kode Peserta & PIN)"]
    end

    classDef mainNode fill:#0B1727,stroke:#0284C7,stroke-width:2px,color:#fff;
    classDef tabNode fill:#F8FAFC,stroke:#475569,stroke-width:1.5px,color:#0F172A;
    classDef targetNode fill:#E0F2FE,stroke:#0284C7,stroke-width:1.5px,color:#0369A1;

    class K mainNode;
    class T1,T2,T3,T4 tabNode;
    class S1,S2,S3,S4 targetNode;
```

---

## 3. Modul Presensi & Portal Peserta (Hybrid Attendance)

```mermaid
flowchart TD
    subgraph Sesi_Buka ["Penyelenggara / Pengajar"]
        P1["Buka Sesi Presensi"] --> P2["Generate Dynamic QR Code<br/>(Kadaluarsa otomatis 60 detik)"]
    end

    subgraph Portal_Siswa ["Aktivitas Peserta di Portal"]
        P2 --> U1["Login Portal Peserta<br/>(Kode Peserta & PIN)"]
        U1 --> U2{"Pilih Aksi Presensi"}
        
        U2 -->|"Hadir Normal"| U3["Scan QR / Direct Check-in"]
        U2 -->|"Halangan Hadir"| U4["Form Pengajuan Izin / Sakit<br/>(Upload Bukti PDF / Foto)"]
        
        U1 --> U5["Akses Menu Tugas"]
        U5 --> U6["Unggah Jawaban Tugas<br/>(Laporan / Dokumen Tugas)"]
    end

    subgraph Verifikasi_Admin ["Validasi & Pencatatan"]
        U3 --> V1["Status: HADIR<br/>(Audit Log Waktu & IP)"]
        U4 --> V2["Review Penyelenggara"]
        V2 -->|"Disetujui"| V3["Status: SAKIT / IZIN"]
        V2 -->|"Ditolak"| V4["Status: ALFA / DITOLAK"]
        U6 --> V5["Status Tugas: DIKUMPULKAN"]
    end

    classDef headerNode fill:#0B1727,stroke:#0284C7,stroke-width:2px,color:#fff;
    classDef actionNode fill:#FEF3C7,stroke:#D97706,stroke-width:1.5px,color:#78350F;
    classDef successNode fill:#DCFCE7,stroke:#16A34A,stroke-width:1.5px,color:#14532D;

    class P1,U1 headerNode;
    class U2,U3,U4,U5,U6 actionNode;
    class V1,V3,V5 successNode;
```

---

## 4. Modul Realisasi Mengajar & Rekapitulasi JP (SIR-JP)

```mermaid
flowchart TD
    subgraph Realisasi_Sesi ["Konfirmasi Jam Mengajar"]
        R1["Sesi Kelas Selesai"] --> R2["Input Form Realisasi SIR-JP"]
        R2 --> R3["Verifikasi Pengajar Hadir<br/>& Jam Riil Mengajar"]
        R3 --> R4["Status Jadwal Update:<br/>TERLAKSANA (6 JP)"]
    end

    subgraph Mesin_Kalkulasi ["Perhitungan Engine Rekapitulasi"]
        R4 --> C1["Kalkulasi Beban Mengajar WI"]
        C1 --> C2["JP Tatap Muka (A)"]
        C1 --> C3["JP Konversi (B)"]
        C2 & C3 --> C4["Total JP Riil = A + B"]
        
        C4 --> C5{"Evaluasi Target<br/>Bulan Berjalan"}
        C5 -->|">= 32 JP"| S_OK["Status: MELEBIHI / MEMENUHI"]
        C5 -->|"< 32 JP"| S_NO["Status: BELUM MEMENUHI"]
    end

    classDef stepNode fill:#0B1727,stroke:#0284C7,stroke-width:2px,color:#fff;
    classDef engineNode fill:#EEF2FF,stroke:#4F46E5,stroke-width:1.5px,color:#312E81;
    classDef alertNode fill:#FEE2E2,stroke:#DC2626,stroke-width:1.5px,color:#7F1D1D;
    classDef greenNode fill:#DCFCE7,stroke:#16A34A,stroke-width:1.5px,color:#14532D;

    class R1,R2 stepNode;
    class C1,C2,C3,C4,C5 engineNode;
    class S_OK greenNode;
    class S_NO alertNode;
```

---

## 5. Modul Dashboard & Ekspor Laporan Akhir (Tahap Akhir)

```mermaid
flowchart TD
    subgraph Sumber_Data ["Data Terkonsolidasi"]
        D1["Data Presensi & Tugas KCLC"]
        D2["Data Realisasi JP SIR-JP"]
    end

    subgraph Dashboard_Visual ["1. Tampilan Dashboard Visual"]
        D1 --> DB1["Dashboard Penyelenggara<br/>(10 Kartu Stat, % Hadir, Evaluasi)"]
        D1 --> DB2["Tabel Statistik Aktivitas Pelatihan<br/>(Dropdown Pagination 5 / 10 Data)"]
        D2 --> DB3["Dashboard Administrator & SIR-JP<br/>(Grafik Bulanan JP & Donut Capaian WI)"]
    end

    subgraph Dokumen_Laporan ["2. Penerbitan Laporan Resmi"]
        D2 --> EXP1["Ekspor Excel Multi-Sheet<br/>(Rekap Individu & Semua Pengajar)"]
        D2 --> EXP2["Ekspor PDF Format Resmi Kemnaker<br/>(Laporan Bulanan & SPTJM Lengkap)"]
        D1 & D2 --> EXP3["Laporan Akhir Terintegrasi<br/>(Arsip & Siap Tanda Tangan)"]
    end

    classDef srcNode fill:#F1F5F9,stroke:#64748B,stroke-width:1.5px,color:#1E293B;
    classDef dashNode fill:#EFF6FF,stroke:#2563EB,stroke-width:1.5px,color:#1E3A8A;
    classDef expNode fill:#0B1727,stroke:#0284C7,stroke-width:2px,color:#fff;

    class D1,D2 srcNode;
    class DB1,DB2,DB3 dashNode;
    class EXP1,EXP2,EXP3 expNode;
```
