<?php

namespace App\Services;

use App\Models\Widyaiswara;
use App\Models\Narasumber;
use App\Models\Pelatihan;
use App\Models\Angkatan;
use App\Models\Jadwal;
use App\Models\RealisasiJadwal;
use App\Models\RealisasiPengajar;
use App\Models\KonversiJp;
use App\Models\AturanJp;
use App\Models\Pengaturan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class JPRecapitulationService
{
    /**
     * Dapatkan nilai batas minimum JP per bulan.
     */
    public function getMinimumJp(): float
    {
        $setting = Pengaturan::get('minimum_jp_bulanan');
        if ($setting !== null && is_numeric($setting)) {
            return (float) $setting;
        }

        $aturan = AturanJp::where('is_active', true)->first();
        return $aturan ? (float) $aturan->minimum_jp_bulanan : 32.00;
    }

    /**
     * Hitung rekapitulasi individual untuk satu Widyaiswara pada bulan & tahun tertentu.
     */
    public function hitungWidyaiswara(int $widyaiswaraId, int $bulan, int $tahun, ?float $minimumJp = null): array
    {
        $minimumJp = $minimumJp ?? $this->getMinimumJp();
        $wi = Widyaiswara::findOrFail($widyaiswaraId);

        // BAGIAN A — Tatap Muka
        // HANYA menghitung jadwal yang REALISASINYA berstatus 'Terlaksana'
        // dan tanggal realisasinya berada pada bulan & tahun yang dipilih!
        $realisasiTatapMuka = RealisasiPengajar::with(['realisasiJadwal.jadwal.mataPelatihan.angkatan.pelatihan'])
            ->where('widyaiswara_id', $widyaiswaraId)
            ->whereHas('realisasiJadwal', function ($q) use ($bulan, $tahun) {
                $q->where('status', 'Terlaksana')
                  ->whereMonth('tanggal_realisasi', $bulan)
                  ->whereYear('tanggal_realisasi', $tahun);
            })
            ->get()
            ->sortBy(function ($item) {
                return $item->realisasiJadwal->tanggal_realisasi;
            });

        $detailA = collect();
        $akumulasiA = 0.00;
        $totalA = 0.00;
        $totalDibayarkanA = 0.00;

        foreach ($realisasiTatapMuka as $rp) {
            $rj = $rp->realisasiJadwal;
            $jadwal = $rj->jadwal;
            $mp = $jadwal->mataPelatihan;
            $angkatan = $mp->angkatan;
            $pelatihan = $angkatan->pelatihan;

            $jpAktual = (float) $rp->jp_aktual;
            $jpDibayarkan = (float) $rp->jp_dibayarkan;

            $akumulasiA += $jpAktual;
            $totalA += $jpAktual;
            $totalDibayarkanA += $jpDibayarkan;

            $detailA->push([
                'id' => $rp->id,
                'realisasi_jadwal_id' => $rj->id,
                'tanggal' => $rj->tanggal_realisasi ? Carbon::parse($rj->tanggal_realisasi)->format('d/m/Y') : '-',
                'tanggal_raw' => $rj->tanggal_realisasi,
                'jam_mulai' => Carbon::parse($rj->jam_mulai_aktual)->format('H:i'),
                'jam_selesai' => Carbon::parse($rj->jam_selesai_aktual)->format('H:i'),
                'nama_pelatihan' => $pelatihan->nama_pelatihan . ' (' . $angkatan->nomor_angkatan . ')',
                'mata_pelatihan' => $mp->nama_mata_pelatihan,
                'jp' => $jpAktual,
                'jp_akumulasi' => $akumulasiA,
                'jp_dibayarkan' => $jpDibayarkan,
                'keterangan' => $rp->keterangan ?? $rj->catatan ?? '-',
                'status_kehadiran' => $rp->status_kehadiran,
            ]);
        }

        // BAGIAN B — Konversi
        // HANYA menghitung konversi berstatus 'Disetujui' pada bulan & tahun yang dipilih
        $konversiList = KonversiJp::where('widyaiswara_id', $widyaiswaraId)
            ->where('status_validasi', 'Disetujui')
            ->whereMonth('tanggal_kegiatan', $bulan)
            ->whereYear('tanggal_kegiatan', $tahun)
            ->orderBy('tanggal_kegiatan', 'asc')
            ->get();

        $detailB = collect();
        $totalB = 0.00;
        $totalDibayarkanB = 0.00;
        $akumulasiB = 0.00;

        foreach ($konversiList as $k) {
            $jpKonversi = (float) $k->jumlah_jp;
            $jpDibayar = (float) $k->jp_dibayarkan;

            $akumulasiB += $jpKonversi;
            $totalB += $jpKonversi;
            $totalDibayarkanB += $jpDibayar;

            $detailB->push([
                'id' => $k->id,
                'tanggal' => Carbon::parse($k->tanggal_kegiatan)->format('d/m/Y'),
                'tanggal_raw' => $k->tanggal_kegiatan,
                'nama_kegiatan' => $k->nama_kegiatan,
                'kategori' => $k->kategori_konversi,
                'jp' => $jpKonversi,
                'jp_akumulasi' => $akumulasiB,
                'jp_dibayarkan' => $jpDibayar,
                'informasi_pembayaran' => $k->informasi_pembayaran ?? '-',
                'nomor_sk' => $k->nomor_sk ?? '-',
                'keterangan' => $k->keterangan ?? '-',
            ]);
        }

        // RINGKASAN & STATUS PENCAPAIAN
        $totalAB = $totalA + $totalB;
        $totalDibayarkan = $totalDibayarkanA + $totalDibayarkanB;
        $kekuranganJp = max(0.00, $minimumJp - $totalAB);
        $kelebihanJp = max(0.00, $totalAB - $minimumJp);

        if ($totalAB < $minimumJp) {
            $statusPencapaian = 'Belum Memenuhi';
            $badgeClass = 'danger';
        } elseif ($totalAB == $minimumJp) {
            $statusPencapaian = 'Memenuhi';
            $badgeClass = 'success';
        } else {
            $statusPencapaian = 'Melebihi';
            $badgeClass = 'primary';
        }

        return [
            'widyaiswara' => $wi,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'nama_bulan' => $this->getNamaBulan($bulan),
            'tatap_muka' => $detailA,
            'konversi' => $detailB,
            'total_a' => round($totalA, 2),
            'total_b' => round($totalB, 2),
            'total_ab' => round($totalAB, 2),
            'total_dibayarkan_a' => round($totalDibayarkanA, 2),
            'total_dibayarkan_b' => round($totalDibayarkanB, 2),
            'total_dibayarkan' => round($totalDibayarkan, 2),
            'minimum_jp' => round($minimumJp, 2),
            'kekurangan_jp' => round($kekuranganJp, 2),
            'kelebihan_jp' => round($kelebihanJp, 2),
            'status_pencapaian' => $statusPencapaian,
            'badge_class' => $badgeClass,
        ];
    }

    /**
     * Rekapitulasi seluruh Widyaiswara dalam satu tabel untuk satu periode bulan & tahun.
     */
    public function rekapSemuaWidyaiswara(int $bulan, int $tahun, ?string $jabatan = null, ?string $search = null): Collection
    {
        $minimumJp = $this->getMinimumJp();

        $query = Widyaiswara::query();

        if ($jabatan) {
            $query->where('jabatan', $jabatan);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('instansi', 'like', "%{$search}%");
            });
        }

        $widyaiswaras = $query->orderBy('nama_lengkap', 'asc')->get();

        $results = collect();

        foreach ($widyaiswaras as $wi) {
            $rekap = $this->hitungWidyaiswara($wi->id, $bulan, $tahun, $minimumJp);
            $results->push([
                'id' => $wi->id,
                'nik' => $wi->nik,
                'nip' => $wi->nip,
                'nama' => $wi->nama_lengkap,
                'nama_gelar' => $wi->nama_gelar,
                'jabatan' => $wi->jabatan,
                'instansi' => $wi->instansi,
                'total_a' => $rekap['total_a'],
                'total_b' => $rekap['total_b'],
                'total_ab' => $rekap['total_ab'],
                'total_dibayarkan' => $rekap['total_dibayarkan'],
                'minimum_jp' => $rekap['minimum_jp'],
                'kekurangan_jp' => $rekap['kekurangan_jp'],
                'kelebihan_jp' => $rekap['kelebihan_jp'],
                'status_pencapaian' => $rekap['status_pencapaian'],
                'badge_class' => $rekap['badge_class'],
            ]);
        }

        return $results;
    }

    /**
     * Rekapitulasi tahunan untuk Widyaiswara (12 bulan breakdown).
     */
    public function rekapTahunanWidyaiswara(int $tahun, ?string $jabatan = null): Collection
    {
        $query = Widyaiswara::query();
        if ($jabatan) {
            $query->where('jabatan', $jabatan);
        }
        $widyaiswaras = $query->orderBy('nama_lengkap', 'asc')->get();

        $results = collect();

        foreach ($widyaiswaras as $wi) {
            $bulanan = [];
            $totalTahunA = 0;
            $totalTahunB = 0;
            $totalTahunAB = 0;
            $totalTahunDibayarkan = 0;

            for ($m = 1; $m <= 12; $m++) {
                $calc = $this->hitungWidyaiswara($wi->id, $m, $tahun);
                $bulanan[$m] = [
                    'total_a' => $calc['total_a'],
                    'total_b' => $calc['total_b'],
                    'total_ab' => $calc['total_ab'],
                    'total_dibayarkan' => $calc['total_dibayarkan'],
                    'status' => $calc['status_pencapaian'],
                ];
                $totalTahunA += $calc['total_a'];
                $totalTahunB += $calc['total_b'];
                $totalTahunAB += $calc['total_ab'];
                $totalTahunDibayarkan += $calc['total_dibayarkan'];
            }

            $results->push([
                'id' => $wi->id,
                'nama_gelar' => $wi->nama_gelar,
                'nip' => $wi->nip,
                'jabatan' => $wi->jabatan,
                'bulanan' => $bulanan,
                'total_a' => round($totalTahunA, 2),
                'total_b' => round($totalTahunB, 2),
                'total_ab' => round($totalTahunAB, 2),
                'total_dibayarkan' => round($totalTahunDibayarkan, 2),
            ]);
        }

        return $results;
    }

    /**
     * Hitung rekapitulasi individual untuk satu Narasumber.
     * (Narasumber TIDAK dikenakan batas minimum 32 JP).
     */
    public function hitungNarasumber(int $narasumberId, int $bulan, int $tahun): array
    {
        $ns = Narasumber::findOrFail($narasumberId);

        $realisasi = RealisasiPengajar::with(['realisasiJadwal.jadwal.mataPelatihan.angkatan.pelatihan'])
            ->where('narasumber_id', $narasumberId)
            ->whereHas('realisasiJadwal', function ($q) use ($bulan, $tahun) {
                $q->where('status', 'Terlaksana')
                  ->whereMonth('tanggal_realisasi', $bulan)
                  ->whereYear('tanggal_realisasi', $tahun);
            })
            ->get()
            ->sortBy(function ($item) {
                return $item->realisasiJadwal->tanggal_realisasi;
            });

        $detail = collect();
        $akumulasi = 0.00;
        $totalJp = 0.00;
        $totalDibayarkan = 0.00;

        foreach ($realisasi as $rp) {
            $rj = $rp->realisasiJadwal;
            $jadwal = $rj->jadwal;
            $mp = $jadwal->mataPelatihan;
            $angkatan = $mp->angkatan;
            $pelatihan = $angkatan->pelatihan;

            $jpAktual = (float) $rp->jp_aktual;
            $jpDibayarkan = (float) $rp->jp_dibayarkan;

            $akumulasi += $jpAktual;
            $totalJp += $jpAktual;
            $totalDibayarkan += $jpDibayarkan;

            $detail->push([
                'id' => $rp->id,
                'realisasi_jadwal_id' => $rj->id,
                'tanggal' => $rj->tanggal_realisasi ? Carbon::parse($rj->tanggal_realisasi)->format('d/m/Y') : '-',
                'tanggal_raw' => $rj->tanggal_realisasi,
                'jam_mulai' => Carbon::parse($rj->jam_mulai_aktual)->format('H:i'),
                'jam_selesai' => Carbon::parse($rj->jam_selesai_aktual)->format('H:i'),
                'nama_pelatihan' => $pelatihan->nama_pelatihan . ' (' . $angkatan->nomor_angkatan . ')',
                'mata_pelatihan' => $mp->nama_mata_pelatihan,
                'jp' => $jpAktual,
                'jp_akumulasi' => $akumulasi,
                'jp_dibayarkan' => $jpDibayarkan,
                'keterangan' => $rp->keterangan ?? $rj->catatan ?? '-',
                'status_kehadiran' => $rp->status_kehadiran,
            ]);
        }

        return [
            'narasumber' => $ns,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'nama_bulan' => $this->getNamaBulan($bulan),
            'tatap_muka' => $detail,
            'total_jp' => round($totalJp, 2),
            'total_dibayarkan' => round($totalDibayarkan, 2),
        ];
    }

    /**
     * Rekapitulasi seluruh Narasumber dalam satu periode bulan & tahun.
     */
    public function rekapSemuaNarasumber(int $bulan, int $tahun, ?string $search = null): Collection
    {
        $query = Narasumber::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('instansi', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $narasumbers = $query->orderBy('nama_lengkap', 'asc')->get();

        $results = collect();

        foreach ($narasumbers as $ns) {
            $rekap = $this->hitungNarasumber($ns->id, $bulan, $tahun);
            $results->push([
                'id' => $ns->id,
                'nik' => $ns->nik,
                'nama' => $ns->nama_lengkap,
                'nama_gelar' => $ns->nama_gelar,
                'instansi' => $ns->instansi,
                'nomor_telepon' => $ns->nomor_telepon,
                'email' => $ns->email,
                'total_jp' => $rekap['total_jp'],
                'total_dibayarkan' => $rekap['total_dibayarkan'],
                'jumlah_kegiatan' => $rekap['tatap_muka']->count(),
            ]);
        }

        return $results;
    }

    /**
     * Dapatkan Statistik Dashboard secara dinamis dari Database.
     */
    public function getDashboardStats(int $bulan, int $tahun): array
    {
        $totalWi = Widyaiswara::where('status', 'Aktif')->count();
        $totalNs = Narasumber::where('status', 'Aktif')->count();
        $totalPelatihan = Pelatihan::count();
        $totalAngkatan = Angkatan::count();

        // Total JP Terealisasi Bulan Ini (WI + NS)
        $jpTatapMukaBulanIni = (float) RealisasiPengajar::whereHas('realisasiJadwal', function ($q) use ($bulan, $tahun) {
            $q->where('status', 'Terlaksana')
              ->whereMonth('tanggal_realisasi', $bulan)
              ->whereYear('tanggal_realisasi', $tahun);
        })->sum('jp_aktual');

        $jpKonversiBulanIni = (float) KonversiJp::where('status_validasi', 'Disetujui')
            ->whereMonth('tanggal_kegiatan', $bulan)
            ->whereYear('tanggal_kegiatan', $tahun)
            ->sum('jumlah_jp');

        $totalJpTerealisasi = $jpTatapMukaBulanIni + $jpKonversiBulanIni;

        $totalJpDibayarkan = (float) RealisasiPengajar::whereHas('realisasiJadwal', function ($q) use ($bulan, $tahun) {
            $q->where('status', 'Terlaksana')
              ->whereMonth('tanggal_realisasi', $bulan)
              ->whereYear('tanggal_realisasi', $tahun);
        })->sum('jp_dibayarkan') + (float) KonversiJp::where('status_validasi', 'Disetujui')
            ->whereMonth('tanggal_kegiatan', $bulan)
            ->whereYear('tanggal_kegiatan', $tahun)
            ->sum('jp_dibayarkan');

        // Total Jadwal Terjadwal vs Terlaksana vs Dibatalkan
        $jadwalBulanIniCount = Jadwal::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->count();

        $jadwalTerlaksanaCount = RealisasiJadwal::whereMonth('tanggal_realisasi', $bulan)
            ->whereYear('tanggal_realisasi', $tahun)
            ->where('status', 'Terlaksana')
            ->count();

        $jadwalDibatalkanCount = RealisasiJadwal::whereMonth('tanggal_realisasi', $bulan)
            ->whereYear('tanggal_realisasi', $tahun)
            ->where('status', 'Dibatalkan')
            ->count();

        // Pencapaian Widyaiswara (Memenuhi vs Belum Memenuhi vs Melebihi)
        $rekapWi = $this->rekapSemuaWidyaiswara($bulan, $tahun);
        $wiMemenuhi = $rekapWi->where('status_pencapaian', 'Memenuhi')->count();
        $wiMelebihi = $rekapWi->where('status_pencapaian', 'Melebihi')->count();
        $wiBelumMemenuhi = $rekapWi->where('status_pencapaian', 'Belum Memenuhi')->count();

        // Chart Data 12 Bulan (Tren JP Tatap Muka & Konversi)
        $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartTatapMuka = [];
        $chartKonversi = [];

        for ($m = 1; $m <= 12; $m++) {
            $tm = (float) RealisasiPengajar::whereHas('realisasiJadwal', function ($q) use ($m, $tahun) {
                $q->where('status', 'Terlaksana')
                  ->whereMonth('tanggal_realisasi', $m)
                  ->whereYear('tanggal_realisasi', $tahun);
            })->sum('jp_aktual');

            $kv = (float) KonversiJp::where('status_validasi', 'Disetujui')
                ->whereMonth('tanggal_kegiatan', $m)
                ->whereYear('tanggal_kegiatan', $tahun)
                ->sum('jumlah_jp');

            $chartTatapMuka[] = round($tm, 1);
            $chartKonversi[] = round($kv, 1);
        }

        // Jadwal Mendatang (Upcoming 5)
        $upcomingJadwals = Jadwal::with(['mataPelatihan.angkatan.pelatihan', 'penugasanPengajar.widyaiswara', 'penugasanPengajar.narasumber'])
            ->where('tanggal', '>=', Carbon::now()->toDateString())
            ->where('status', 'Terjadwal')
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->take(5)
            ->get();

        return [
            'total_widyaiswara' => $totalWi,
            'total_narasumber' => $totalNs,
            'total_pelatihan' => $totalPelatihan,
            'total_angkatan' => $totalAngkatan,
            'total_jp_terealisasi' => round($totalJpTerealisasi, 2),
            'total_jp_dibayarkan' => round($totalJpDibayarkan, 2),
            'jp_tatap_muka' => round($jpTatapMukaBulanIni, 2),
            'jp_konversi' => round($jpKonversiBulanIni, 2),
            'jadwal_bulan_ini_count' => $jadwalBulanIniCount,
            'jadwal_terlaksana_count' => $jadwalTerlaksanaCount,
            'jadwal_dibatalkan_count' => $jadwalDibatalkanCount,
            'wi_memenuhi' => $wiMemenuhi,
            'wi_melebihi' => $wiMelebihi,
            'wi_belum_memenuhi' => $wiBelumMemenuhi,
            'chart_labels' => $chartLabels,
            'chart_tatap_muka' => $chartTatapMuka,
            'chart_konversi' => $chartKonversi,
            'upcoming_jadwals' => $upcomingJadwals,
            'top_widyaiswara' => $rekapWi->sortByDesc('total_ab')->take(5)->values(),
        ];
    }

    public function getNamaBulan(int $bulan): string
    {
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $namaBulan[$bulan] ?? 'Bulan ' . $bulan;
    }
}
