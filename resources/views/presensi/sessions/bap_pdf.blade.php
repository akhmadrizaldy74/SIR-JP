<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Berita Acara Pembelajaran - {{ $attendanceSession->judul_sesi }}</title>
    <style>
        body { font-family: sans-serif; font-size: 9pt; color: #1e293b; margin: 0; padding: 14px; }
        .kop { text-align: center; border-bottom: 2.5px solid #0f172a; padding-bottom: 8px; margin-bottom: 14px; }
        .kop h2 { margin: 0; font-size: 12.5pt; text-transform: uppercase; letter-spacing: 0.3px; }
        .kop h3 { margin: 3px 0 2px 0; font-size: 11pt; text-transform: uppercase; color: #0f172a; }
        .kop p { margin: 0; font-size: 8.5pt; color: #475569; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .meta-table td { padding: 3px 6px; vertical-align: top; font-size: 8.8pt; border: none; }
        .meta-label { width: 140px; font-weight: bold; color: #334155; }
        .summary-box { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .summary-box th, .summary-box td { border: 1px solid #94a3b8; padding: 5px; text-align: center; font-size: 8.2pt; }
        .summary-box th { background-color: #f1f5f9; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .data-table th, .data-table td { border: 1px solid #94a3b8; padding: 4px 6px; font-size: 8.2pt; }
        .data-table th { background-color: #e2e8f0; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .photo-grid { width: 100%; margin-top: 12px; border-collapse: collapse; }
        .photo-grid td { width: 50%; padding: 6px; vertical-align: top; border: 1px solid #cbd5e1; text-align: center; }
        .photo-grid img { max-width: 100%; max-height: 150px; object-fit: contain; }
        .footer-ttd { width: 100%; margin-top: 24px; border-collapse: collapse; }
        .footer-ttd td { width: 50%; text-align: center; vertical-align: top; font-size: 8.8pt; border: none; }
        .badge { padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 7.5pt; }
        .b-hadir { background: #d1fae5; color: #065f46; }
        .b-terlambat { background: #dbeafe; color: #1e40af; }
        .b-izin { background: #fef3c7; color: #92400e; }
        .b-sakit { background: #fce7f3; color: #9d174d; }
        .b-alpa { background: #fee2e2; color: #991b1b; }
        .b-disp { background: #ede9fe; color: #5b21b6; }
    </style>
</head>
<body>
    @php
        $pelatihan = $attendanceSession->jadwal->mataPelatihan->angkatan->pelatihan->nama_pelatihan ?? '-';
        $angkatan = $attendanceSession->jadwal->mataPelatihan->angkatan->nomor_angkatan ?? '-';
        $mapel = $attendanceSession->jadwal->mataPelatihan->nama_mata_pelatihan ?? '-';
        $jp = $attendanceSession->jadwal->jp_rencana ?? 1;
        $penugasan = $attendanceSession->jadwal->penugasanPengajar ?? null;
        $pengajarNama = $penugasan?->pengajar_nama ?? ($attendanceSession->creator->name ?? '-');
        $pengajarNip = $penugasan?->widyaiswara?->nip ?? ($attendanceSession->creator->nik ?? '-');
        $ruangan = $attendanceSession->kelasMapel->ruangan->nama_ruangan ?? ($attendanceSession->jadwal->ruangan ?? '-');
    @endphp

    <div class="kop">
        <h2>PUSAT PENGEMBANGAN SUMBER DAYA MANUSIA KETENAGAKERJAAN</h2>
        <h3>BERITA ACARA PEMBELAJARAN &amp; DAFTAR HADIR PESERTA</h3>
        <p>Sistem Informasi Rekapitulasi Jam Pelajaran (SIR-JP) &bull; KCLC Learning Center</p>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Program Pelatihan</td>
            <td>: {{ $pelatihan }} ({{ $angkatan }})</td>
            <td class="meta-label">Hari / Tanggal</td>
            <td>: {{ $attendanceSession->tanggal ? $attendanceSession->tanggal->translatedFormat('l, d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Mata Pelatihan</td>
            <td>: {{ $mapel }} ({{ $jp }} JP)</td>
            <td class="meta-label">Waktu Pembelajaran</td>
            <td>: {{ substr($attendanceSession->jam_mulai, 0, 5) }} - {{ substr($attendanceSession->jam_selesai, 0, 5) }} WIB</td>
        </tr>
        <tr>
            <td class="meta-label">Judul Pertemuan</td>
            <td>: {{ $attendanceSession->judul_sesi }}</td>
            <td class="meta-label">Kelas / Ruangan</td>
            <td>: {{ $attendanceSession->kelasMapel->nama_kelas ?? '-' }} / {{ $ruangan }}</td>
        </tr>
        <tr>
            <td class="meta-label">Nama Pengajar</td>
            <td>: {{ $pengajarNama }}</td>
            <td class="meta-label">Status Sesi &amp; Realisasi</td>
            <td>: {{ $attendanceSession->status }} ({{ $attendanceSession->jadwal?->realisasi ? 'Terealisasi ' . $attendanceSession->jadwal->realisasi->jp_aktual . ' JP' : 'Belum Terealisasi' }})</td>
        </tr>
    </table>

    <table class="summary-box">
        <thead>
            <tr>
                <th>Total Peserta</th>
                <th>Hadir</th>
                <th>Terlambat</th>
                <th>Izin</th>
                <th>Sakit</th>
                <th>Dispensasi</th>
                <th>Alpa</th>
                <th>Belum Presensi</th>
                <th>% Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>{{ $stats['total_peserta'] }}</strong></td>
                <td>{{ $stats['hadir'] }}</td>
                <td>{{ $stats['terlambat'] }}</td>
                <td>{{ $stats['izin'] }}</td>
                <td>{{ $stats['sakit'] }}</td>
                <td>{{ $stats['dispensasi'] }}</td>
                <td>{{ $stats['alpa'] }}</td>
                <td>{{ $stats['belum_presensi'] }}</td>
                <td><strong>{{ $stats['persen_kehadiran'] }}%</strong></td>
            </tr>
        </tbody>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 26px;">No</th>
                <th style="width: 110px;">NIP</th>
                <th>Nama Peserta</th>
                <th>Instansi</th>
                <th style="width: 75px;">Status</th>
                <th style="width: 65px;">Check-in</th>
                <th>Keterangan / Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stats['pesertas'] as $pst)
                @php
                    $rec = $stats['presensis']->get($pst->id);
                    $status = $rec ? $rec->status_kehadiran : 'Belum Presensi';
                    $badgeClass = match($status) {
                        'Hadir' => 'b-hadir',
                        'Terlambat' => 'b-terlambat',
                        'Izin' => 'b-izin',
                        'Sakit' => 'b-sakit',
                        'Alpa' => 'b-alpa',
                        'Dispensasi' => 'b-disp',
                        default => '',
                    };
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $pst->nip }}</td>
                    <td><strong>{{ $pst->nama }}</strong></td>
                    <td>{{ $pst->instansi ?: '-' }}</td>
                    <td class="text-center"><span class="badge {{ $badgeClass }}">{{ $status }}</span></td>
                    <td class="text-center">{{ $rec && $rec->waktu_presensi ? substr($rec->waktu_presensi, 0, 5) . ' WIB' : '-' }}</td>
                    <td>
                        {{ $rec->keterangan ?? '-' }}
                        @if($rec && $rec->status_verifikasi === 'Menunggu Verifikasi')
                            <em>(Menunggu Verifikasi)</em>
                        @elseif($rec && $rec->status_verifikasi === 'Ditolak')
                            <em>(Bukti Ditolak)</em>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Belum ada peserta terdaftar pada kelas ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(!empty($docPhotos))
        <h4 style="margin: 14px 0 6px 0; font-size: 9.5pt;">Lampiran Dokumentasi Kegiatan Pembelajaran</h4>
        <table class="photo-grid">
            @foreach(array_chunk($docPhotos, 2) as $row)
                <tr>
                    @foreach($row as $photo)
                        <td>
                            <img src="{{ $photo['src'] }}" alt="Dokumentasi">
                            <div style="font-size: 7.5pt; color: #475569; margin-top: 4px;">
                                <strong>{{ $photo['kategori'] }}:</strong> {{ $photo['keterangan'] }}
                            </div>
                        </td>
                    @endforeach
                    @if(count($row) === 1)
                        <td></td>
                    @endif
                </tr>
            @endforeach
        </table>
    @endif

    <table class="footer-ttd">
        <tr>
            <td>
                Mengetahui,<br>Penyelenggara Pelatihan
                <div style="height: 55px;"></div>
                <strong>{{ auth()->user()->name ?? 'Penyelenggara Diklat' }}</strong><br>
                <span style="font-size: 8pt; color: #64748b;">NIP/NIK. {{ auth()->user()->nik ?? '-' }}</span>
            </td>
            <td>
                Jakarta, {{ now()->translatedFormat('d F Y') }}<br>Pengajar / Widyaiswara
                <div style="height: 55px;"></div>
                <strong>{{ $pengajarNama }}</strong><br>
                <span style="font-size: 8pt; color: #64748b;">NIP/NIK. {{ $pengajarNip }}</span>
            </td>
        </tr>
    </table>
</body>
</html>
