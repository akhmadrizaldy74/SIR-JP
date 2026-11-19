<table>
    <tr>
        <th colspan="16" style="font-size: 14pt; font-weight: bold; text-align: center;">{{ strtoupper(\App\Models\Pengaturan::get('instansi_nama', 'Pusat Pengembangan Sumber Daya Manusia')) }}</th>
    </tr>
    <tr>
        <th colspan="16" style="font-size: 12pt; font-weight: bold; text-align: center;">REKAPITULASI JAM PELAJARAN (JP) TAHUNAN WIDYAISWARA</th>
    </tr>
    <tr>
        <th colspan="16" style="font-size: 10pt; text-align: center;">Tahun Anggaran: {{ $tahun }}</th>
    </tr>
    <tr><td colspan="16"></td></tr>

    <tr style="background-color: #183B65; color: #FFFFFF; font-weight: bold; text-align: center;">
        <th>No.</th>
        <th>Nama Widyaiswara</th>
        <th>NIP</th>
        <th>Jabatan</th>
        @for($m = 1; $m <= 12; $m++)
            <th>{{ substr((new \App\Services\JPRecapitulationService)->getNamaBulan($m), 0, 3) }}</th>
        @endfor
        <th>Total A</th>
        <th>Total B</th>
        <th>Total JP (A+B)</th>
        <th>Total Dibayarkan</th>
    </tr>
    @foreach($rekapTahunan as $idx => $wi)
        <tr>
            <td style="text-align: center;">{{ $idx + 1 }}</td>
            <td>{{ $wi['nama_gelar'] }}</td>
            <td style="text-align: center;">{{ $wi['nip'] ?? '-' }}</td>
            <td>{{ $wi['jabatan'] }}</td>
            @for($m = 1; $m <= 12; $m++)
                <td style="text-align: center;">{{ $wi['bulanan'][$m]['total_ab'] > 0 ? $wi['bulanan'][$m]['total_ab'] : '-' }}</td>
            @endfor
            <td style="text-align: center;">{{ $wi['total_a'] }}</td>
            <td style="text-align: center;">{{ $wi['total_b'] }}</td>
            <td style="text-align: center; font-weight: bold;">{{ $wi['total_ab'] }}</td>
            <td style="text-align: center;">{{ $wi['total_dibayarkan'] }}</td>
        </tr>
    @endforeach
</table>
