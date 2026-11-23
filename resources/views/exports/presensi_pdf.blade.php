<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Presensi Peserta - {{ $kelas->nama_kelas }}</title>
    <style>
        body { font-family: sans-serif; font-size: 8.5pt; color: #1e293b; margin: 0; padding: 15px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #0f172a; padding-bottom: 8px; }
        .header h2 { margin: 0 0 4px 0; font-size: 13pt; text-transform: uppercase; }
        .header p { margin: 0; font-size: 9pt; color: #475569; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #94a3b8; padding: 4px 5px; }
        th { background-color: #f1f5f9; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .badge-h { background-color: #d1fae5; color: #065f46; font-weight: bold; }
        .badge-t { background-color: #dbeafe; color: #1e40af; font-weight: bold; }
        .badge-i { background-color: #fef3c7; color: #92400e; font-weight: bold; }
        .badge-s { background-color: #fce7f3; color: #9d174d; font-weight: bold; }
        .badge-a { background-color: #fee2e2; color: #991b1b; font-weight: bold; }
        .badge-d { background-color: #ede9fe; color: #5b21b6; font-weight: bold; }
        .footer { margin-top: 25px; width: 100%; }
        .ttd-box { float: right; width: 220px; text-align: center; font-size: 9pt; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PUSAT PENGEMBANGAN SUMBER DAYA MANUSIA KETENAGAKERJAAN</h2>
        <p>REKAPITULASI PRESENSI PESERTA PELATIHAN (KCLC LEARNING CENTER)</p>
        <p style="margin-top: 4px; font-weight: bold;">
            Kelas: {{ $kelas->nama_kelas }} | Angkatan: {{ $kelas->angkatan->nomor_angkatan ?? '-' }} | Program: {{ $kelas->pelatihan->nama_pelatihan ?? '-' }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">No</th>
                <th rowspan="2" style="width: 85px;">NIP</th>
                <th rowspan="2" style="width: 140px;">Nama Peserta</th>
                <th colspan="{{ max(1, $sessions->count()) }}">Sesi Pembelajaran</th>
                <th colspan="6">Rekapitulasi Status</th>
                <th rowspan="2" style="width: 45px;">% Hadir</th>
            </tr>
            <tr>
                @forelse($sessions as $idx => $s)
                    <th style="font-size: 7pt; width: 30px;">
                        S{{ $idx + 1 }}<br>{{ $s->tanggal->format('d/m') }}
                    </th>
                @empty
                    <th style="font-size: 7.5pt;">Belum ada sesi</th>
                @endforelse
                <th style="font-size: 7.5pt; width: 22px; background-color: #d1fae5;">H</th>
                <th style="font-size: 7.5pt; width: 22px; background-color: #dbeafe;">T</th>
                <th style="font-size: 7.5pt; width: 22px; background-color: #fef3c7;">I</th>
                <th style="font-size: 7.5pt; width: 22px; background-color: #fce7f3;">S</th>
                <th style="font-size: 7.5pt; width: 22px; background-color: #fee2e2;">A</th>
                <th style="font-size: 7.5pt; width: 22px; background-color: #ede9fe;">D</th>
            </tr>
        </thead>
        <tbody>
            @forelse($matrix as $item)
                @php $pst = $item['peserta']; @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $pst->nip }}</td>
                    <td>{{ $pst->nama }}</td>
                    @forelse($sessions as $s)
                        @php
                            $rec = $item['sessions'][$s->id] ?? null;
                            $st = $rec ? $rec->status_kehadiran : '-';
                            $class = match($st) {
                                'Hadir' => 'badge-h',
                                'Terlambat' => 'badge-t',
                                'Izin' => 'badge-i',
                                'Sakit' => 'badge-s',
                                'Alpa' => 'badge-a',
                                'Dispensasi' => 'badge-d',
                                default => '',
                            };
                            $code = match($st) {
                                'Hadir' => 'H',
                                'Terlambat' => 'T',
                                'Izin' => 'I',
                                'Sakit' => 'S',
                                'Alpa' => 'A',
                                'Dispensasi' => 'D',
                                default => '-',
                            };
                        @endphp
                        <td class="text-center {{ $class }}">{{ $code }}</td>
                    @empty
                        <td class="text-center">-</td>
                    @endforelse
                    <td class="text-center">{{ $item['hadir'] }}</td>
                    <td class="text-center">{{ $item['terlambat'] }}</td>
                    <td class="text-center">{{ $item['izin'] }}</td>
                    <td class="text-center">{{ $item['sakit'] }}</td>
                    <td class="text-center">{{ $item['alpa'] }}</td>
                    <td class="text-center">{{ $item['dispensasi'] }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ $item['persen'] }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 10 + max(1, $sessions->count()) }}" class="text-center">Belum ada data peserta terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 10px; font-size: 7.5pt; color: #64748b;">
        <strong>Keterangan Kode:</strong> H = Hadir, T = Terlambat, I = Izin, S = Sakit, A = Alpa, D = Dispensasi.
    </div>

    <div class="footer">
        <div class="ttd-box">
            <p>Jakarta, {{ now()->translatedFormat('d F Y') }}<br>Penyelenggara Pelatihan,</p>
            <div style="height: 50px;"></div>
            <p style="font-weight: bold; text-decoration: underline;">{{ auth()->user()->name ?? 'Penyelenggara Diklat' }}</p>
            <p style="margin-top: -3px; font-size: 8pt; color: #64748b;">NIP. {{ auth()->user()->nip ?? '-' }}</p>
        </div>
    </div>
</body>
</html>
