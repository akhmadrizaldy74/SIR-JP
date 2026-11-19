<table>
    <tr>
        <th colspan="9" style="font-size: 14pt; font-weight: bold; text-align: center;">{{ strtoupper(\App\Models\Pengaturan::get('instansi_nama', 'Pusat Pengembangan Sumber Daya Manusia')) }}</th>
    </tr>
    <tr>
        <th colspan="9" style="font-size: 12pt; font-weight: bold; text-align: center;">REKAPITULASI JAM PELAJARAN (JP) SELURUH WIDYAISWARA</th>
    </tr>
    <tr>
        <th colspan="9" style="font-size: 10pt; text-align: center;">Periode: {{ strtoupper($namaBulan) }} {{ $tahun }} - Batas Minimum Wajib: {{ $minimumJp }} JP</th>
    </tr>
    <tr><td colspan="9"></td></tr>

    <tr style="background-color: #183B65; color: #FFFFFF; font-weight: bold; text-align: center;">
        <th>No.</th>
        <th>Nama Widyaiswara</th>
        <th>NIP</th>
        <th>Jabatan</th>
        <th>Tatap Muka (A)</th>
        <th>Konversi (B)</th>
        <th>Total (A + B)</th>
        <th>JP Dibayarkan</th>
        <th>Kekurangan JP</th>
        <th>Status Pencapaian</th>
    </tr>
    @foreach($rekap as $idx => $r)
        <tr>
            <td style="text-align: center;">{{ $idx + 1 }}</td>
            <td>{{ $r['nama_gelar'] }}</td>
            <td style="text-align: center;">{{ $r['nip'] ?? '-' }}</td>
            <td>{{ $r['jabatan'] }}</td>
            <td style="text-align: center;">{{ $r['total_a'] }}</td>
            <td style="text-align: center;">{{ $r['total_b'] }}</td>
            <td style="text-align: center; font-weight: bold;">{{ $r['total_ab'] }}</td>
            <td style="text-align: center;">{{ $r['total_dibayarkan'] }}</td>
            <td style="text-align: center; color: {{ $r['kekurangan_jp'] > 0 ? '#DC2626' : '#16A34A' }};">
                {{ $r['kekurangan_jp'] > 0 ? $r['kekurangan_jp'] : 0 }}
            </td>
            <td style="text-align: center;">{{ $r['status_pencapaian'] }}</td>
        </tr>
    @endforeach
    <tr><td colspan="9"></td></tr>
    <tr>
        <td colspan="6"></td>
        <td colspan="3" style="text-align: center;">{{ \App\Models\Pengaturan::get('pimpinan_kota', 'Jakarta') }}, {{ date('d F Y') }}</td>
    </tr>
    <tr>
        <td colspan="6"></td>
        <td colspan="3" style="text-align: center; font-weight: bold;">{{ \App\Models\Pengaturan::get('pimpinan_jabatan', 'Kepala Pusat') }}</td>
    </tr>
    <tr><td colspan="9" style="height: 40px;"></td></tr>
    <tr>
        <td colspan="6"></td>
        <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ \App\Models\Pengaturan::get('pimpinan_nama', 'Pimpinan Instansi') }}</td>
    </tr>
    <tr>
        <td colspan="6"></td>
        <td colspan="3" style="text-align: center;">NIP. {{ \App\Models\Pengaturan::get('pimpinan_nip', '-') }}</td>
    </tr>
</table>
