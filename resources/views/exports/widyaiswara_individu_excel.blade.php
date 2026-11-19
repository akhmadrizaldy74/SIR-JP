<table>
    <tr>
        <th colspan="7" style="font-size: 14pt; font-weight: bold; text-align: center;">{{ strtoupper(\App\Models\Pengaturan::get('instansi_nama', 'Pusat Pengembangan Sumber Daya Manusia')) }}</th>
    </tr>
    <tr>
        <th colspan="7" style="font-size: 12pt; font-weight: bold; text-align: center;">REKAPITULASI BEBAN JAM PELAJARAN (JP) WIDYAISWARA</th>
    </tr>
    <tr>
        <th colspan="7" style="font-size: 10pt; text-align: center;">Periode: {{ strtoupper($rekap['nama_bulan']) }} {{ $tahun }}</th>
    </tr>
    <tr><td colspan="7"></td></tr>

    <!-- IDENTITAS -->
    <tr>
        <td style="font-weight: bold;">Nama Lengkap:</td>
        <td colspan="3">{{ $rekap['widyaiswara']->nama_gelar }}</td>
        <td style="font-weight: bold;">Jabatan:</td>
        <td colspan="2">{{ $rekap['widyaiswara']->jabatan }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">NIP:</td>
        <td colspan="3">{{ $rekap['widyaiswara']->nip ?? '-' }}</td>
        <td style="font-weight: bold;">Batas Minimum JP:</td>
        <td colspan="2">{{ $rekap['minimum_jp'] }} JP / Bulan</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">NIK:</td>
        <td colspan="3">{{ $rekap['widyaiswara']->nik }}</td>
        <td style="font-weight: bold;">Instansi:</td>
        <td colspan="2">{{ $rekap['widyaiswara']->instansi }}</td>
    </tr>
    <tr><td colspan="7"></td></tr>

    <!-- BAGIAN A -->
    <tr style="background-color: #DBEAFE;">
        <th colspan="7" style="font-weight: bold;">BAGIAN A - KEGIATAN TATAP MUKA (PEMBELAJARAN)</th>
    </tr>
    <tr style="background-color: #F1F5F9; font-weight: bold; text-align: center;">
        <th>No.</th>
        <th>Tanggal</th>
        <th>Nama Pelatihan dan Angkatan</th>
        <th>Mata Pelatihan</th>
        <th>JP</th>
        <th>JP Akumulasi</th>
        <th>JP Dibayarkan</th>
        <th>Keterangan</th>
    </tr>
    @forelse($rekap['tatap_muka'] as $idxA => $tm)
        <tr>
            <td style="text-align: center;">{{ $idxA + 1 }}</td>
            <td style="text-align: center;">{{ $tm['tanggal'] }}</td>
            <td>{{ $tm['nama_pelatihan'] }}</td>
            <td>{{ $tm['mata_pelatihan'] }}</td>
            <td style="text-align: center; font-weight: bold;">{{ $tm['jp'] }}</td>
            <td style="text-align: center;">{{ $tm['jp_akumulasi'] }}</td>
            <td style="text-align: center;">{{ $tm['jp_dibayarkan'] }}</td>
            <td>{{ $tm['keterangan'] }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="8" style="text-align: center; font-style: italic;">Tidak ada kegiatan tatap muka yang direalisasikan pada bulan ini.</td>
        </tr>
    @endforelse
    <tr style="font-weight: bold; background-color: #F8FAFC;">
        <td colspan="4" style="text-align: right;">JUMLAH BAGIAN A (TATAP MUKA):</td>
        <td style="text-align: center;">{{ $rekap['total_a'] }}</td>
        <td></td>
        <td style="text-align: center;">{{ $rekap['total_dibayarkan_a'] }}</td>
        <td></td>
    </tr>
    <tr><td colspan="7"></td></tr>

    <!-- BAGIAN B -->
    <tr style="background-color: #DBEAFE;">
        <th colspan="7" style="font-weight: bold;">BAGIAN B - KEGIATAN KONVERSI JAM PELAJARAN</th>
    </tr>
    <tr style="background-color: #F1F5F9; font-weight: bold; text-align: center;">
        <th>No.</th>
        <th>Tanggal</th>
        <th colspan="2">Nama Kegiatan Konversi</th>
        <th>Kategori</th>
        <th>JP</th>
        <th>JP Dibayarkan</th>
        <th>Keterangan / Info Pembayaran</th>
    </tr>
    @forelse($rekap['konversi'] as $idxB => $kv)
        <tr>
            <td style="text-align: center;">{{ $idxB + 1 }}</td>
            <td style="text-align: center;">{{ $kv['tanggal'] }}</td>
            <td colspan="2">{{ $kv['nama_kegiatan'] }}</td>
            <td>{{ $kv['kategori'] }}</td>
            <td style="text-align: center; font-weight: bold;">{{ $kv['jp'] }}</td>
            <td style="text-align: center;">{{ $kv['jp_dibayarkan'] }}</td>
            <td>{{ $kv['informasi_pembayaran'] }} (SK: {{ $kv['nomor_sk'] }})</td>
        </tr>
    @empty
        <tr>
            <td colspan="8" style="text-align: center; font-style: italic;">Tidak ada kegiatan konversi yang divalidasi pada bulan ini.</td>
        </tr>
    @endforelse
    <tr style="font-weight: bold; background-color: #F8FAFC;">
        <td colspan="5" style="text-align: right;">JUMLAH BAGIAN B (KONVERSI):</td>
        <td style="text-align: center;">{{ $rekap['total_b'] }}</td>
        <td style="text-align: center;">{{ $rekap['total_dibayarkan_b'] }}</td>
        <td></td>
    </tr>
    <tr><td colspan="7"></td></tr>

    <!-- RINGKASAN -->
    <tr style="background-color: #E2E8F0; font-weight: bold;">
        <th colspan="7">RINGKASAN AKUMULASI JAM PELAJARAN</th>
    </tr>
    <tr>
        <td colspan="4">Total Jam Pelajaran Tatap Muka (Bagian A)</td>
        <td style="text-align: center; font-weight: bold;">{{ $rekap['total_a'] }} JP</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="4">Total Jam Pelajaran Konversi (Bagian B)</td>
        <td style="text-align: center; font-weight: bold;">{{ $rekap['total_b'] }} JP</td>
        <td colspan="2"></td>
    </tr>
    <tr style="background-color: #EFF6FF; font-weight: bold;">
        <td colspan="4">TOTAL JAM PELAJARAN (A + B)</td>
        <td style="text-align: center; font-size: 11pt; color: #183B65;">{{ $rekap['total_ab'] }} JP</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="4">Total JP yang Dibayarkan</td>
        <td style="text-align: center; font-weight: bold;">{{ $rekap['total_dibayarkan'] }} JP</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="4">Batas Minimum Jam Pelajaran / Bulan</td>
        <td style="text-align: center;">{{ $rekap['minimum_jp'] }} JP</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="4">Kekurangan Jam Pelajaran</td>
        <td style="text-align: center; font-weight: bold;">
            {{ $rekap['kekurangan_jp'] }} JP
        </td>
        <td colspan="2"></td>
    </tr>
    <tr style="font-weight: bold;">
        <td colspan="4">STATUS PENCAPAIAN</td>
        <td style="text-align: center; background-color: #DBEAFE;">{{ strtoupper($rekap['status_pencapaian']) }}</td>
        <td colspan="2"></td>
    </tr>
    <tr><td colspan="7"></td></tr>

    <!-- AREA TANDA TANGAN -->
    <tr>
        <td colspan="3" style="text-align: center;">Mengetahui/Menyetujui,</td>
        <td></td>
        <td colspan="3" style="text-align: center;">{{ \App\Models\Pengaturan::get('pimpinan_kota', 'Jakarta') }}, {{ date('d') }} {{ $rekap['nama_bulan'] }} {{ $tahun }}</td>
    </tr>
    <tr>
        <td colspan="3" style="text-align: center; font-weight: bold;">{{ \App\Models\Pengaturan::get('pimpinan_jabatan', 'Kepala Pusat') }}</td>
        <td></td>
        <td colspan="3" style="text-align: center; font-weight: bold;">Widyaiswara yang Bersangkutan,</td>
    </tr>
    <tr><td colspan="7" style="height: 50px;"></td></tr>
    <tr>
        <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ \App\Models\Pengaturan::get('pimpinan_nama', 'Pimpinan Instansi') }}</td>
        <td></td>
        <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $rekap['widyaiswara']->nama_gelar }}</td>
    </tr>
    <tr>
        <td colspan="3" style="text-align: center;">NIP. {{ \App\Models\Pengaturan::get('pimpinan_nip', '-') }}</td>
        <td></td>
        <td colspan="3" style="text-align: center;">NIP. {{ $rekap['widyaiswara']->nip ?? '-' }}</td>
    </tr>
</table>
