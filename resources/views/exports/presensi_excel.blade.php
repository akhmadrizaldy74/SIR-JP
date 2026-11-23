<table>
    <thead>
        <tr>
            <th colspan="{{ 8 + $sessions->count() }}" style="font-size: 14pt; font-weight: bold; text-align: center;">
                REKAPITULASI KEHADIRAN PESERTA DIKLAT KCLC
            </th>
        </tr>
        <tr>
            <th colspan="{{ 8 + $sessions->count() }}" style="font-size: 11pt; text-align: center;">
                Kelas: {{ $kelas->nama_kelas }} | Angkatan: {{ $kelas->angkatan->nomor_angkatan ?? '-' }} | Pelatihan: {{ $kelas->pelatihan->nama_pelatihan ?? '-' }}
            </th>
        </tr>
        <tr></tr>
        <tr style="background-color: #E2E8F0; font-weight: bold;">
            <th style="border: 1px solid #000; text-align: center;">No</th>
            <th style="border: 1px solid #000; text-align: center;">NIP</th>
            <th style="border: 1px solid #000; text-align: center;">Nama Peserta</th>
            <th style="border: 1px solid #000; text-align: center;">Instansi</th>
            @foreach($sessions as $idx => $s)
                <th style="border: 1px solid #000; text-align: center;">
                    Sesi {{ $idx + 1 }}<br>
                    <small>{{ $s->tanggal->format('d/m') }}</small>
                </th>
            @endforeach
            <th style="border: 1px solid #000; text-align: center; background-color: #D1FAE5;">Hadir</th>
            <th style="border: 1px solid #000; text-align: center; background-color: #DBEAFE;">Terlambat</th>
            <th style="border: 1px solid #000; text-align: center; background-color: #FEF3C7;">Izin</th>
            <th style="border: 1px solid #000; text-align: center; background-color: #FCE7F3;">Sakit</th>
            <th style="border: 1px solid #000; text-align: center; background-color: #FEE2E2;">Alpa</th>
            <th style="border: 1px solid #000; text-align: center; background-color: #EDE9FE;">Dispensasi</th>
            <th style="border: 1px solid #000; text-align: center;">% Kehadiran</th>
        </tr>
    </thead>
    <tbody>
        @foreach($matrix as $mIndex => $item)
            @php
                $pst = $item['peserta'];
                // Formula injection protection
                $safeName = preg_replace('/^[=\+\-@\t\r]/', "'$0", $pst->nama);
                $safeNip = preg_replace('/^[=\+\-@\t\r]/', "'$0", $pst->nip);
                $safeInstansi = preg_replace('/^[=\+\-@\t\r]/', "'$0", $pst->instansi ?? '-');
            @endphp
            <tr>
                <td style="border: 1px solid #000; text-align: center;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid #000;">{{ $safeNip }}</td>
                <td style="border: 1px solid #000;">{{ $safeName }}</td>
                <td style="border: 1px solid #000;">{{ $safeInstansi }}</td>
                @foreach($sessions as $s)
                    @php
                        $rec = $item['sessions'][$s->id] ?? null;
                        $st = $rec ? $rec->status_kehadiran : '-';
                        $color = match($st) {
                            'Hadir' => '#D1FAE5',
                            'Terlambat' => '#DBEAFE',
                            'Izin' => '#FEF3C7',
                            'Sakit' => '#FCE7F3',
                            'Alpa' => '#FEE2E2',
                            'Dispensasi' => '#EDE9FE',
                            default => '#FFFFFF',
                        };
                    @endphp
                    <td style="border: 1px solid #000; text-align: center; background-color: {{ $color }};">
                        {{ $st === '-' ? '-' : substr($st, 0, 1) }}
                    </td>
                @endforeach
                <td style="border: 1px solid #000; text-align: center;">{{ $item['hadir'] }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $item['terlambat'] }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $item['izin'] }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $item['sakit'] }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $item['alpa'] }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $item['dispensasi'] }}</td>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $item['persen'] }}%</td>
            </tr>
        @endforeach
    </tbody>
</table>
