<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Aktivitas Narasumber - {{ $rekap['narasumber']->nama_lengkap }}</title>
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
        .identitas-box {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .identitas-box td {
            padding: 3px 5px;
            font-size: 9pt;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #CBD5E1;
            padding: 5px 6px;
            font-size: 8.5pt;
        }
        table.data-table th {
            background-color: #F8FAFC;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signature-table td {
            text-align: center;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ \App\Models\Pengaturan::get('instansi_nama', 'Pusat Pengembangan Sumber Daya Manusia') }}</h2>
        <h3>LAPORAN REALISASI JAM MENGAJAR NARASUMBER</h3>
        <p style="margin: 0; font-size: 8.5pt; color: #64748B;">Periode Bulan: {{ strtoupper($rekap['nama_bulan']) }} {{ $tahun }}</p>
    </div>

    <table class="identitas-box">
        <tr>
            <td style="width: 18%; font-weight: bold;">Nama Narasumber</td>
            <td style="width: 32%;">: {{ $rekap['narasumber']->nama_gelar }}</td>
            <td style="width: 15%; font-weight: bold;">Instansi Asal</td>
            <td style="width: 35%;">: {{ $rekap['narasumber']->instansi }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">NIK</td>
            <td>: {{ $rekap['narasumber']->nik }}</td>
            <td style="font-weight: bold;">Kontak</td>
            <td>: {{ $rekap['narasumber']->nomor_telepon ?? '-' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No.</th>
                <th style="width: 75px;">Tanggal</th>
                <th>Program Pelatihan & Angkatan</th>
                <th>Mata Pelatihan</th>
                <th style="width: 60px;">JP Aktual</th>
                <th style="width: 70px;">JP Dibayar</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekap['tatap_muka'] as $idx => $tm)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center">{{ $tm['tanggal'] }}</td>
                    <td>{{ $tm['nama_pelatihan'] }}</td>
                    <td>{{ $tm['mata_pelatihan'] }}</td>
                    <td class="text-center fw-bold">{{ $tm['jp'] }}</td>
                    <td class="text-center">{{ $tm['jp_dibayarkan'] }}</td>
                    <td>{{ $tm['keterangan'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="font-style: italic; color: #64748B; padding: 12px;">Tidak ada sesi mengajar pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #F8FAFC; font-weight: bold;">
                <td colspan="4" class="text-right">TOTAL:</td>
                <td class="text-center">{{ $rekap['total_jp'] }} JP</td>
                <td class="text-center">{{ $rekap['total_dibayarkan'] }} JP</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <table class="signature-table">
        <tr>
            <td style="width: 50%;">
                Mengetahui,<br>
                <strong>{{ \App\Models\Pengaturan::get('pimpinan_jabatan', 'Kepala Pusat') }}</strong>
                <br><br><br><br>
                <strong><u>{{ \App\Models\Pengaturan::get('pimpinan_nama', 'Pimpinan Instansi') }}</u></strong><br>
                NIP. {{ \App\Models\Pengaturan::get('pimpinan_nip', '-') }}
            </td>
            <td style="width: 50%;">
                {{ \App\Models\Pengaturan::get('pimpinan_kota', 'Jakarta') }}, {{ date('d') }} {{ $rekap['nama_bulan'] }} {{ $tahun }}<br>
                Narasumber,<br><br><br><br>
                <strong><u>{{ $rekap['narasumber']->nama_gelar }}</u></strong>
            </td>
        </tr>
    </table>
</body>
</html>
