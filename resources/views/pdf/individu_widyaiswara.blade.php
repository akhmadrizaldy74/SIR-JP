<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rekapitulasi JP Wajib Widyaiswara - {{ $rekap['widyaiswara']->nama_gelar }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            color: #1E293B;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
        }
        .header h2 {
            margin: 0 0 4px 0;
            font-size: 13pt;
            font-weight: bold;
            color: #183B65;
            letter-spacing: 0.5px;
        }
        .header h3 {
            margin: 0;
            font-size: 10.5pt;
            font-weight: bold;
            color: #334155;
        }
        .identitas-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 9pt;
            border-collapse: collapse;
        }
        .identitas-table td {
            padding: 2px 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #475569;
            padding: 4px 6px;
            font-size: 8pt;
        }
        table.data-table th {
            background-color: #F1F5F9;
            font-weight: bold;
            text-align: center;
            color: #1E293B;
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
            font-size: 8.5pt;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>REKAPITULASI JP WAJIB WIDYAISWARA</h2>
        <h3>BULAN: {{ strtoupper($rekap['nama_bulan']) }} {{ $tahun }}</h3>
    </div>

    <!-- IDENTITAS -->
    <table class="identitas-table">
        <tr>
            <td style="width: 90px; font-weight: bold;">NAMA</td>
            <td>: {{ strtoupper($rekap['widyaiswara']->nama_gelar) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">JABATAN</td>
            <td>: {{ strtoupper($rekap['widyaiswara']->jabatan) }}</td>
        </tr>
    </table>

    <!-- TABEL REKAPITULASI -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 70px;">TANGGAL</th>
                <th>NAMA PELATIHAN</th>
                <th>MATA PELATIHAN</th>
                <th style="width: 35px;">JP</th>
                <th style="width: 60px;">JP AKUMULASI</th>
                <th style="width: 60px;">JP DIBAYARKAN</th>
                <th>KETERANGAN</th>
                <th style="width: 90px;">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border-right: none;"></td>
                <td colspan="7" class="fw-bold" style="border-left: none; background-color: #F8FAFC;">A. TATAP MUKA</td>
            </tr>
            @forelse($rekap['tatap_muka'] as $tm)
                <tr>
                    <td class="text-center">{{ $tm['tanggal'] }}</td>
                    <td>{{ $tm['nama_pelatihan'] }}</td>
                    <td>{{ $tm['mata_pelatihan'] }}</td>
                    <td class="text-center fw-bold">{{ $tm['jp'] > 0 ? $tm['jp'] : '' }}</td>
                    <td class="text-center">{{ $tm['jp_akumulasi'] > 0 ? $tm['jp_akumulasi'] : '' }}</td>
                    <td class="text-center">{{ $tm['jp_dibayarkan'] > 0 ? $tm['jp_dibayarkan'] : '' }}</td>
                    <td>{{ $tm['keterangan'] !== '-' ? $tm['keterangan'] : '' }}</td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="font-style: italic; color: #64748B;">Tidak ada kegiatan tatap muka pada bulan ini.</td>
                </tr>
            @endforelse
            <tr class="fw-bold" style="background-color: #F1F5F9;">
                <td></td>
                <td>TOTAL A</td>
                <td></td>
                <td class="text-center">{{ $rekap['total_a'] }}</td>
                <td></td>
                <td class="text-center">{{ $rekap['total_dibayarkan_a'] > 0 ? $rekap['total_dibayarkan_a'] : 0 }}</td>
                <td></td>
                <td class="text-center">Jam Wajib {{ (int)$rekap['minimum_jp'] }} JP</td>
            </tr>
            <tr>
                <td style="border-right: none;"></td>
                <td class="fw-bold" style="border-left: none; border-right: none;">B. KONVERSI</td>
                <td></td>
                <td class="text-center">0</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @if(count($rekap['konversi']) > 0)
                @foreach($rekap['konversi'] as $kv)
                    <tr>
                        <td class="text-center">{{ $kv['tanggal'] }}</td>
                        <td colspan="2">{{ $kv['nama_kegiatan'] }}</td>
                        <td class="text-center fw-bold">{{ $kv['jp'] }}</td>
                        <td></td>
                        <td class="text-center">{{ $kv['jp_dibayarkan'] > 0 ? $kv['jp_dibayarkan'] : '' }}</td>
                        <td colspan="2">{{ $kv['keterangan'] }}</td>
                    </tr>
                @endforeach
            @endif
            <tr class="fw-bold" style="background-color: #F1F5F9;">
                <td></td>
                <td>TOTAL B</td>
                <td></td>
                <td class="text-center">{{ $rekap['total_b'] }}</td>
                <td></td>
                <td class="text-center">{{ $rekap['total_dibayarkan_b'] > 0 ? $rekap['total_dibayarkan_b'] : 0 }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr class="fw-bold" style="background-color: #E2E8F0;">
                <td></td>
                <td>TOTAL A DAN B</td>
                <td></td>
                <td class="text-center">{{ $rekap['total_ab'] }}</td>
                <td></td>
                <td class="text-center">{{ $rekap['total_dibayarkan'] > 0 ? $rekap['total_dibayarkan'] : 0 }}</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- TANDA TANGAN (Format Resmi Kepala PPSDM) -->
    <table class="signature-table">
        <tr>
            <td style="width: 55%;"></td>
            <td style="width: 45%; text-align: center;">
                {{ \App\Models\Pengaturan::get('pimpinan_kota', 'Jakarta') }}, {{ date('j') }} {{ $rekap['nama_bulan'] }} {{ $tahun }}<br>
                <strong>Kepala {{ \App\Models\Pengaturan::get('instansi_nama', 'Pusat Pengembangan SDM') }}</strong><br>
                <strong>{{ \App\Models\Pengaturan::get('instansi_sub', 'Ketenagakerjaan') }}</strong>
                <br><br><br><br>
                <strong><u>{{ \App\Models\Pengaturan::get('pimpinan_nama', 'Dr. Narsih, S.Pd., M.M.') }}</u></strong><br>
                NIP. {{ \App\Models\Pengaturan::get('pimpinan_nip', '197202111998032002') }}
            </td>
        </tr>
    </table>
</body>
</html>
