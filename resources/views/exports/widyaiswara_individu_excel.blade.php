<table>
    <tr>
        <th colspan="8" style="font-family: Calibri; font-size: 14pt; font-weight: bold; text-align: center;">REKAPITULASI JP WAJIB WIDYAISWARA</th>
    </tr>
    <tr>
        <th colspan="8" style="font-family: Calibri; font-size: 11pt; font-weight: bold; text-align: center;">BULAN: {{ strtoupper($rekap['nama_bulan']) }} {{ $tahun }}</th>
    </tr>
    <tr>
        <td colspan="8"></td>
    </tr>
    <tr>
        <td style="font-family: Calibri; font-weight: bold;">NAMA</td>
        <td colspan="7" style="font-family: Calibri; font-weight: bold;">: {{ strtoupper($rekap['widyaiswara']->nama_gelar) }}</td>
    </tr>
    <tr>
        <td style="font-family: Calibri; font-weight: bold;">JABATAN</td>
        <td colspan="7" style="font-family: Calibri; font-weight: bold;">: {{ strtoupper($rekap['widyaiswara']->jabatan) }}</td>
    </tr>
    <tr>
        <td colspan="8"></td>
    </tr>
    <tr style="font-family: Calibri; font-weight: bold; text-align: center; border: 1px solid #000000;">
        <th style="border: 1px solid #000000; text-align: center;">TANGGAL</th>
        <th style="border: 1px solid #000000; text-align: center;">NAMA PELATIHAN</th>
        <th style="border: 1px solid #000000; text-align: center;">MATA PELATIHAN</th>
        <th style="border: 1px solid #000000; text-align: center;">JP</th>
        <th style="border: 1px solid #000000; text-align: center;">JP AKUMULASI</th>
        <th style="border: 1px solid #000000; text-align: center;">JP DIBAYARKAN</th>
        <th style="border: 1px solid #000000; text-align: center;">KETERANGAN</th>
        <th style="border: 1px solid #000000; text-align: center;">KETERANGAN</th>
    </tr>
    <tr>
        <td style="border-left: 1px solid #000000;"></td>
        <td colspan="7" style="font-family: Calibri; font-weight: bold; border-right: 1px solid #000000;">A. TATAP MUKA</td>
    </tr>
    @forelse($rekap['tatap_muka'] as $tm)
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">{{ $tm['tanggal'] }}</td>
            <td style="border: 1px solid #000000;">{{ $tm['nama_pelatihan'] }}</td>
            <td style="border: 1px solid #000000;">{{ $tm['mata_pelatihan'] }}</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $tm['jp'] > 0 ? $tm['jp'] : '' }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $tm['jp_akumulasi'] > 0 ? $tm['jp_akumulasi'] : '' }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $tm['jp_dibayarkan'] > 0 ? $tm['jp_dibayarkan'] : '' }}</td>
            <td style="border: 1px solid #000000;">{{ $tm['keterangan'] !== '-' ? $tm['keterangan'] : '' }}</td>
            <td style="border: 1px solid #000000;"></td>
        </tr>
    @empty
        <tr>
            <td colspan="8" style="border: 1px solid #000000; text-align: center; font-style: italic;">Tidak ada kegiatan tatap muka pada bulan ini.</td>
        </tr>
    @endforelse
    <tr style="font-family: Calibri; font-weight: bold;">
        <td style="border: 1px solid #000000;"></td>
        <td style="border: 1px solid #000000; font-weight: bold;">TOTAL A</td>
        <td style="border: 1px solid #000000;"></td>
        <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $rekap['total_a'] }}</td>
        <td style="border: 1px solid #000000;"></td>
        <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $rekap['total_dibayarkan_a'] > 0 ? $rekap['total_dibayarkan_a'] : 0 }}</td>
        <td style="border: 1px solid #000000;"></td>
        <td style="border: 1px solid #000000; text-align: center;">Jam Wajib {{ (int)$rekap['minimum_jp'] }} JP</td>
    </tr>
    <tr>
        <td style="border-left: 1px solid #000000;"></td>
        <td style="font-family: Calibri; font-weight: bold; border: 1px solid #000000;">B. KONVERSI</td>
        <td style="border: 1px solid #000000;"></td>
        <td style="border: 1px solid #000000; text-align: center;">0</td>
        <td style="border: 1px solid #000000;"></td>
        <td style="border: 1px solid #000000;"></td>
        <td style="border: 1px solid #000000;"></td>
        <td style="border: 1px solid #000000;"></td>
    </tr>
    @if(count($rekap['konversi']) > 0)
        @foreach($rekap['konversi'] as $kv)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $kv['tanggal'] }}</td>
                <td colspan="2" style="border: 1px solid #000000;">{{ $kv['nama_kegiatan'] }}</td>
                <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $kv['jp'] }}</td>
                <td style="border: 1px solid #000000;"></td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $kv['jp_dibayarkan'] > 0 ? $kv['jp_dibayarkan'] : '' }}</td>
                <td colspan="2" style="border: 1px solid #000000;">{{ $kv['keterangan'] }}</td>
            </tr>
        @endforeach
    @endif
    <tr style="font-family: Calibri; font-weight: bold;">
        <td style="border: 1px solid #000000;"></td>
        <td style="border: 1px solid #000000; font-weight: bold;">TOTAL  B</td>
        <td style="border: 1px solid #000000;"></td>
        <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $rekap['total_b'] }}</td>
        <td style="border: 1px solid #000000;"></td>
        <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $rekap['total_dibayarkan_b'] > 0 ? $rekap['total_dibayarkan_b'] : 0 }}</td>
        <td style="border: 1px solid #000000;"></td>
        <td style="border: 1px solid #000000;"></td>
    </tr>
    <tr style="font-family: Calibri; font-weight: bold;">
        <td style="border: 1px solid #000000;"></td>
        <td style="border: 1px solid #000000; font-weight: bold;">TOTAL A DAN B</td>
        <td style="border: 1px solid #000000;"></td>
        <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $rekap['total_ab'] }}</td>
        <td style="border: 1px solid #000000;"></td>
        <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $rekap['total_dibayarkan'] > 0 ? $rekap['total_dibayarkan'] : 0 }}</td>
        <td style="border: 1px solid #000000;"></td>
        <td style="border: 1px solid #000000;"></td>
    </tr>
    <tr><td colspan="8"></td></tr>
    <tr>
        <td colspan="3"></td>
        <td colspan="5" style="font-family: Calibri; text-align: center;">Jakarta, {{ date('j') }} {{ $rekap['nama_bulan'] }} {{ $tahun }}</td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td colspan="5" style="font-family: Calibri; font-weight: bold; text-align: center;">Kepala {{ \App\Models\Pengaturan::get('instansi_nama', 'Pusat Pengembangan SDM') }}</td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td colspan="5" style="font-family: Calibri; font-weight: bold; text-align: center;">{{ \App\Models\Pengaturan::get('instansi_sub', 'Ketenagakerjaan') }}</td>
    </tr>
    <tr><td colspan="8" style="height: 25px;"></td></tr>
    <tr><td colspan="8" style="height: 25px;"></td></tr>
    <tr>
        <td colspan="3"></td>
        <td colspan="5" style="font-family: Calibri; font-weight: bold; text-align: center;">{{ \App\Models\Pengaturan::get('pimpinan_nama', 'Dr. Narsih, S.Pd., M.M.') }}</td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td colspan="5" style="font-family: Calibri; text-align: center;">NIP. {{ \App\Models\Pengaturan::get('pimpinan_nip', '197202111998032002') }}</td>
    </tr>
</table>