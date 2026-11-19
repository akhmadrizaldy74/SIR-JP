<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Rekapitulasi JP Individu - {{ $rekap['widyaiswara']->nama_lengkap }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #1E293B;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #183B65;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h2 {
            margin: 0 0 4px 0;
            font-size: 13pt;
            color: #183B65;
            text-transform: uppercase;
        }
        .header h3 {
            margin: 0 0 4px 0;
            font-size: 11pt;
            font-weight: bold;
        }
        .header p {
            margin: 0;
            font-size: 8.5pt;
            color: #64748B;
        }
        .identitas-box {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .identitas-box td {
            padding: 2px 4px;
            font-size: 8.5pt;
        }
        .section-title {
            font-size: 9pt;
            font-weight: bold;
            color: #183B65;
            margin: 10px 0 4px 0;
            text-transform: uppercase;
            background-color: #EFF6FF;
            padding: 4px 6px;
            border-left: 3px solid #2563EB;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #CBD5E1;
            padding: 4px 5px;
            font-size: 8pt;
        }
        table.data-table th {
            background-color: #F8FAFC;
            font-weight: bold;
            text-align: center;
            color: #334155;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .summary-box {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 15px;
        }
        .summary-box td {
            padding: 3px 6px;
            font-size: 8.5pt;
            border: 1px solid #E2E8F0;
        }
        .signature-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signature-table td {
            text-align: center;
            font-size: 8.5pt;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ \App\Models\Pengaturan::get('instansi_nama', 'Pusat Pengembangan Sumber Daya Manusia') }}</h2>
        <h3>REKAPITULASI BEBAN JAM PELAJARAN (JP) WIDYAISWARA</h3>
        <p>Periode Bulan: {{ strtoupper($rekap['nama_bulan']) }} {{ $tahun }} &bull; Ketentuan Beban Wajib: {{ $rekap['minimum_jp'] }} JP / Bulan</p>
    </div>

    <!-- IDENTITAS -->
    <table class="identitas-box">
        <tr>
            <td style="width: 15%; font-weight: bold;">Nama Lengkap</td>
            <td style="width: 35%;">: {{ $rekap['widyaiswara']->nama_gelar }}</td>
            <td style="width: 15%; font-weight: bold;">Jabatan</td>
            <td style="width: 35%;">: {{ $rekap['widyaiswara']->jabatan }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">NIP</td>
            <td>: {{ $rekap['widyaiswara']->nip ?? '-' }}</td>
            <td style="font-weight: bold;">Instansi</td>
            <td>: {{ $rekap['widyaiswara']->instansi }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">NIK</td>
            <td>: {{ $rekap['widyaiswara']->nik }}</td>
            <td style="font-weight: bold;">Status Pegawai</td>
            <td>: {{ $rekap['widyaiswara']->status }}</td>
        </tr>
    </table>

    <!-- BAGIAN A: TATAP MUKA -->
    <div class="section-title">BAGIAN A &mdash; KEGIATAN TATAP MUKA (PEMBELAJARAN)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No.</th>
                <th style="width: 65px;">Tanggal</th>
                <th>Program Pelatihan & Angkatan</th>
                <th>Mata Pelatihan</th>
                <th style="width: 45px;">JP</th>
                <th style="width: 55px;">Akumulasi</th>
                <th style="width: 55px;">Dibayar</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekap['tatap_muka'] as $idxA => $tm)
                <tr>
                    <td class="text-center">{{ $idxA + 1 }}</td>
                    <td class="text-center">{{ $tm['tanggal'] }}</td>
                    <td>{{ $tm['nama_pelatihan'] }}</td>
                    <td>{{ $tm['mata_pelatihan'] }}</td>
                    <td class="text-center fw-bold">{{ $tm['jp'] }}</td>
                    <td class="text-center">{{ $tm['jp_akumulasi'] }}</td>
                    <td class="text-center">{{ $tm['jp_dibayarkan'] }}</td>
                    <td>{{ $tm['keterangan'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="font-style: italic; color: #64748B;">Tidak ada kegiatan tatap muka pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #F8FAFC; font-weight: bold;">
                <td colspan="4" class="text-right">JUMLAH BAGIAN A (TATAP MUKA):</td>
                <td class="text-center">{{ $rekap['total_a'] }} JP</td>
                <td></td>
                <td class="text-center">{{ $rekap['total_dibayarkan_a'] }} JP</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- BAGIAN B: KONVERSI -->
    <div class="section-title">BAGIAN B &mdash; KEGIATAN KONVERSI JAM PELAJARAN</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No.</th>
                <th style="width: 65px;">Tanggal</th>
                <th>Nama Kegiatan Konversi</th>
                <th>Kategori</th>
                <th style="width: 45px;">JP</th>
                <th style="width: 55px;">Akumulasi</th>
                <th style="width: 55px;">Dibayar</th>
                <th>Keterangan / Info Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekap['konversi'] as $idxB => $kv)
                <tr>
                    <td class="text-center">{{ $idxB + 1 }}</td>
                    <td class="text-center">{{ $kv['tanggal'] }}</td>
                    <td>{{ $kv['nama_kegiatan'] }}</td>
                    <td>{{ $kv['kategori'] }}</td>
                    <td class="text-center fw-bold">{{ $kv['jp'] }}</td>
                    <td class="text-center">{{ $kv['jp_akumulasi'] }}</td>
                    <td class="text-center">{{ $kv['jp_dibayarkan'] }}</td>
                    <td>{{ $kv['informasi_pembayaran'] }} (SK: {{ $kv['nomor_sk'] }})</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="font-style: italic; color: #64748B;">Tidak ada kegiatan konversi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #F8FAFC; font-weight: bold;">
                <td colspan="4" class="text-right">JUMLAH BAGIAN B (KONVERSI):</td>
                <td class="text-center">{{ $rekap['total_b'] }} JP</td>
                <td></td>
                <td class="text-center">{{ $rekap['total_dibayarkan_b'] }} JP</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- RINGKASAN -->
    <table class="summary-box">
        <tr style="background-color: #F1F5F9; font-weight: bold;">
            <td colspan="2">RINGKASAN AKUMULASI JAM PELAJARAN</td>
        </tr>
        <tr>
            <td style="width: 70%;">1. Total Jam Pelajaran Tatap Muka (Bagian A)</td>
            <td class="text-right fw-bold">{{ $rekap['total_a'] }} JP</td>
        </tr>
        <tr>
            <td>2. Total Jam Pelajaran Konversi (Bagian B)</td>
            <td class="text-right fw-bold">{{ $rekap['total_b'] }} JP</td>
        </tr>
        <tr style="background-color: #EFF6FF; font-weight: bold;">
            <td>TOTAL JAM PELAJARAN (A + B)</td>
            <td class="text-right" style="color: #183B65; font-size: 9.5pt;">{{ $rekap['total_ab'] }} JP</td>
        </tr>
        <tr>
            <td>Total JP yang Dibayarkan (Honorarium)</td>
            <td class="text-right fw-bold">{{ $rekap['total_dibayarkan'] }} JP</td>
        </tr>
        <tr>
            <td>Batas Beban Minimum Jam Pelajaran / Bulan</td>
            <td class="text-right">{{ $rekap['minimum_jp'] }} JP</td>
        </tr>
        <tr>
            <td>Kekurangan Jam Pelajaran</td>
            <td class="text-right fw-bold" style="color: {{ $rekap['kekurangan_jp'] > 0 ? '#DC2626' : '#16A34A' }};">
                {{ $rekap['kekurangan_jp'] }} JP
            </td>
        </tr>
        <tr style="font-weight: bold; background-color: #F1F5F9;">
            <td>STATUS PENCAPAIAN BEBAN MENGAJAR</td>
            <td class="text-right">{{ strtoupper($rekap['status_pencapaian']) }}</td>
        </tr>
    </table>

    <!-- TANDA TANGAN -->
    <table class="signature-table">
        <tr>
            <td style="width: 50%;">
                Mengetahui / Menyetujui,<br>
                <strong>{{ \App\Models\Pengaturan::get('pimpinan_jabatan', 'Kepala Pusat') }}</strong>
                <br><br><br><br>
                <strong><u>{{ \App\Models\Pengaturan::get('pimpinan_nama', 'Pimpinan Instansi') }}</u></strong><br>
                NIP. {{ \App\Models\Pengaturan::get('pimpinan_nip', '-') }}
            </td>
            <td style="width: 50%;">
                {{ \App\Models\Pengaturan::get('pimpinan_kota', 'Jakarta') }}, {{ date('d') }} {{ $rekap['nama_bulan'] }} {{ $tahun }}<br>
                Widyaiswara yang bersangkutan,
                <br><br><br><br>
                <strong><u>{{ $rekap['widyaiswara']->nama_gelar }}</u></strong><br>
                NIP. {{ $rekap['widyaiswara']->nip ?? '-' }}
            </td>
        </tr>
    </table>
</body>
</html>
