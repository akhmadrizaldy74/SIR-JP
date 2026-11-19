<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rekapitulasi Seluruh Widyaiswara - {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        @page { size: landscape; margin: 15mm 10mm; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            color: #1E293B;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #183B65;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .header h2 {
            margin: 0 0 3px 0;
            font-size: 13pt;
            color: #183B65;
            text-transform: uppercase;
        }
        .header h3 {
            margin: 0;
            font-size: 10.5pt;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #CBD5E1;
            padding: 4px 6px;
            font-size: 8pt;
        }
        table.data-table th {
            background-color: #F1F5F9;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .signature-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signature-table td {
            text-align: center;
            font-size: 8.5pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ \App\Models\Pengaturan::get('instansi_nama', 'Pusat Pengembangan Sumber Daya Manusia') }}</h2>
        <h3>REKAPITULASI CAPAIAN JAM PELAJARAN (JP) SELURUH WIDYAISWARA</h3>
        <p style="margin: 3px 0 0 0; font-size: 8pt; color: #64748B;">Periode Bulan: {{ strtoupper($namaBulan) }} {{ $tahun }} &bull; Batas Minimum Wajib: {{ $minimumJp }} JP/Bulan</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">No.</th>
                <th rowspan="2" class="text-start">Nama Widyaiswara & NIP</th>
                <th rowspan="2">Jabatan</th>
                <th colspan="3">Perhitungan Jam Pelajaran (JP)</th>
                <th rowspan="2" style="width: 75px;">JP Dibayarkan</th>
                <th rowspan="2" style="width: 60px;">Batas Min</th>
                <th rowspan="2" style="width: 65px;">Kekurangan</th>
                <th rowspan="2" style="width: 90px;">Status</th>
            </tr>
            <tr>
                <th style="width: 70px;">Tatap Muka (A)</th>
                <th style="width: 70px;">Konversi (B)</th>
                <th style="width: 80px;">Total (A+B)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekap as $idx => $r)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $r['nama_gelar'] }}</strong><br>
                        <span style="color: #64748B; font-size: 7.5pt;">NIP. {{ $r['nip'] ?? '-' }}</span>
                    </td>
                    <td>{{ $r['jabatan'] }}</td>
                    <td class="text-center">{{ $r['total_a'] }}</td>
                    <td class="text-center">{{ $r['total_b'] }}</td>
                    <td class="text-center fw-bold">{{ $r['total_ab'] }}</td>
                    <td class="text-center">{{ $r['total_dibayarkan'] }}</td>
                    <td class="text-center">{{ $r['minimum_jp'] }}</td>
                    <td class="text-center" style="color: {{ $r['kekurangan_jp'] > 0 ? '#DC2626' : '#16A34A' }}; font-weight: bold;">
                        {{ $r['kekurangan_jp'] > 0 ? $r['kekurangan_jp'] : '-' }}
                    </td>
                    <td class="text-center fw-bold">{{ $r['status_pencapaian'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td style="width: 65%;"></td>
            <td style="width: 35%;">
                {{ \App\Models\Pengaturan::get('pimpinan_kota', 'Jakarta') }}, {{ date('d') }} {{ $namaBulan }} {{ $tahun }}<br>
                <strong>{{ \App\Models\Pengaturan::get('pimpinan_jabatan', 'Kepala Pusat') }}</strong>
                <br><br><br><br>
                <strong><u>{{ \App\Models\Pengaturan::get('pimpinan_nama', 'Pimpinan Instansi') }}</u></strong><br>
                NIP. {{ \App\Models\Pengaturan::get('pimpinan_nip', '-') }}
            </td>
        </tr>
    </table>
</body>
</html>
