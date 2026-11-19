<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rekapitulasi Tahunan Widyaiswara {{ $tahun }}</title>
    <style>
        @page { size: landscape; margin: 15mm 10mm; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8pt;
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
        .header h3 { margin: 0; font-size: 10.5pt; }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #CBD5E1;
            padding: 4px 5px;
            font-size: 7.5pt;
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
        .signature-table td { text-align: center; font-size: 8pt; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ \App\Models\Pengaturan::get('instansi_nama', 'Pusat Pengembangan Sumber Daya Manusia') }}</h2>
        <h3>MATRIKS REKAPITULASI JAM PELAJARAN (JP) TAHUNAN WIDYAISWARA</h3>
        <p style="margin: 3px 0 0 0; font-size: 8pt; color: #64748B;">Tahun Anggaran: {{ $tahun }}</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 20px;">No.</th>
                <th rowspan="2" class="text-start">Nama Widyaiswara</th>
                <th rowspan="2" style="width: 85px;">Jabatan</th>
                <th colspan="12">Bulan (Total JP A + B)</th>
                <th colspan="3">Total {{ $tahun }}</th>
            </tr>
            <tr>
                @for($m = 1; $m <= 12; $m++)
                    <th style="width: 32px;">{{ substr((new \App\Services\JPRecapitulationService)->getNamaBulan($m), 0, 3) }}</th>
                @endfor
                <th style="width: 45px;">Tot. A</th>
                <th style="width: 45px;">Tot. B</th>
                <th style="width: 55px;">(A+B)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapTahunan as $idx => $wi)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $wi['nama_gelar'] }}</strong><br>
                        <span style="color: #64748B; font-size: 7pt;">NIP. {{ $wi['nip'] ?? '-' }}</span>
                    </td>
                    <td>{{ $wi['jabatan'] }}</td>
                    @for($m = 1; $m <= 12; $m++)
                        @php $val = $wi['bulanan'][$m]['total_ab']; @endphp
                        <td class="text-center {{ $val >= 32 ? 'fw-bold' : '' }}">
                            {{ $val > 0 ? $val : '-' }}
                        </td>
                    @endfor
                    <td class="text-center">{{ $wi['total_a'] }}</td>
                    <td class="text-center">{{ $wi['total_b'] }}</td>
                    <td class="text-center fw-bold">{{ $wi['total_ab'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td style="width: 70%;"></td>
            <td style="width: 30%;">
                {{ \App\Models\Pengaturan::get('pimpinan_kota', 'Jakarta') }}, 31 Desember {{ $tahun }}<br>
                <strong>{{ \App\Models\Pengaturan::get('pimpinan_jabatan', 'Kepala Pusat') }}</strong>
                <br><br><br><br>
                <strong><u>{{ \App\Models\Pengaturan::get('pimpinan_nama', 'Pimpinan Instansi') }}</u></strong><br>
                NIP. {{ \App\Models\Pengaturan::get('pimpinan_nip', '-') }}
            </td>
        </tr>
    </table>
</body>
</html>
