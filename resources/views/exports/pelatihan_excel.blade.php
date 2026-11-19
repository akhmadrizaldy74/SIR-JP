<table>
    <tr>
        <th colspan="7" style="font-size: 14pt; font-weight: bold; text-align: center;">{{ strtoupper(\App\Models\Pengaturan::get('instansi_nama', 'PPSDM')) }}</th>
    </tr>
    <tr>
        <th colspan="7" style="font-size: 12pt; font-weight: bold; text-align: center;">LAPORAN PROGRAM PELATIHAN DAN ANGKATAN</th>
    </tr>
    <tr><td colspan="7"></td></tr>

    <tr style="background-color: #183B65; color: #FFFFFF; font-weight: bold; text-align: center;">
        <th>No.</th>
        <th>Kode Pelatihan</th>
        <th>Nama Pelatihan</th>
        <th>Kategori</th>
        <th>Penyelenggara</th>
        <th>Periode Pelaksanaan</th>
        <th>Status</th>
    </tr>
    @foreach($pelatihans as $idx => $pel)
        <tr>
            <td style="text-align: center;">{{ $idx + 1 }}</td>
            <td style="text-align: center;">{{ $pel->kode_pelatihan }}</td>
            <td>{{ $pel->nama_pelatihan }}</td>
            <td>{{ $pel->kategori }}</td>
            <td>{{ $pel->penyelenggara }}</td>
            <td style="text-align: center;">{{ $pel->tanggal_mulai->format('d/m/Y') }} s.d. {{ $pel->tanggal_selesai->format('d/m/Y') }}</td>
            <td style="text-align: center;">{{ $pel->status }}</td>
        </tr>
    @endforeach
</table>
