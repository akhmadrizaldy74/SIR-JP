<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ruangan;
use App\Models\Pelatihan;
use App\Models\Angkatan;
use App\Models\MataPelatihan;
use App\Models\KelasMapel;
use App\Models\KelasMapelPengajar;
use App\Models\Peserta;
use App\Models\Presensi;
use App\Models\AspekPenilaianSikap;
use App\Models\FormEvaluasi;
use App\Models\Narasumber;
use App\Models\Widyaiswara;

class KCLCScreenshotSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ruangan
        $ruangans = [
            'Ruang Kelas Online',
            'Ruang Kelas Wangsa',
            'Ruang Kelas Ryuji',
            'Ruang Kelas Tara',
            'Ruang Kelas Akarya',
            'Ruang Aula Graha Utama',
        ];
        foreach ($ruangans as $r) {
            Ruangan::firstOrCreate(['nama_ruangan' => $r], [
                'kapasitas' => '40 Peserta',
                'lokasi' => 'Gedung Diklat Lt. 2',
                'is_aktif' => true,
            ]);
        }
        $ruangOnline = Ruangan::where('nama_ruangan', 'Ruang Kelas Online')->first();

        // 2. Pelatihan & Angkatan (Sesuai KCLC)
        $pelatihans = [
            [
                'nama' => 'Pelatihan Teknis Antar Kerja PPPK Pengantar Kerja',
                'kode' => 'PK-PPPK-2026',
                'angkatans' => [
                    ['nomor' => 'Pengantar Kerja PPPK', 'tgl_mulai' => '2026-11-18', 'tgl_selesai' => '2026-11-25', 'peserta' => 223],
                    ['nomor' => 'Pengantar Kerja A.70', 'tgl_mulai' => '2026-10-01', 'tgl_selesai' => '2026-10-20', 'peserta' => 39],
                    ['nomor' => 'Pengantar Kerja A.69', 'tgl_mulai' => '2026-09-05', 'tgl_selesai' => '2026-09-25', 'peserta' => 34],
                ]
            ],
            [
                'nama' => 'Pelatihan Penjenjangan Jabatan Fungsional Pengantar Kerja Ahli Muda',
                'kode' => 'PK-MUDA-2026',
                'angkatans' => [
                    ['nomor' => 'Pengantar Kerja Ahli Muda A.1', 'tgl_mulai' => '2026-11-23', 'tgl_selesai' => '2026-12-04', 'peserta' => 30],
                ]
            ],
            [
                'nama' => 'Pelatihan Pembentukan Jabatan Fungsional Pengawas Ketenagakerjaan',
                'kode' => 'WASNAKER-2026',
                'angkatans' => [
                    ['nomor' => 'Pengawas Ketenagakerjaan A.112', 'tgl_mulai' => '2026-11-02', 'tgl_selesai' => '2026-11-19', 'peserta' => 37],
                    ['nomor' => 'Pengawas Ketenagakerjaan A.111', 'tgl_mulai' => '2026-10-10', 'tgl_selesai' => '2026-10-30', 'peserta' => 42],
                ]
            ],
            [
                'nama' => 'Pelatihan Pembentukan Jabatan Fungsional Instruktur',
                'kode' => 'INSTRUKTUR-2026',
                'angkatans' => [
                    ['nomor' => 'Instruktur A.7', 'tgl_mulai' => '2026-10-05', 'tgl_selesai' => '2026-10-25', 'peserta' => 48],
                    ['nomor' => 'Instruktur A.6', 'tgl_mulai' => '2026-09-01', 'tgl_selesai' => '2026-09-20', 'peserta' => 46],
                    ['nomor' => 'Instruktur A.5', 'tgl_mulai' => '2026-08-01', 'tgl_selesai' => '2026-08-20', 'peserta' => 47],
                ]
            ],
            [
                'nama' => 'Pelatihan Dasar MHI',
                'kode' => 'MHI-DASAR-2026',
                'angkatans' => [
                    ['nomor' => 'Mediator Hubungan Industrial A.104', 'tgl_mulai' => '2026-10-01', 'tgl_selesai' => '2026-10-22', 'peserta' => 38],
                    ['nomor' => 'Mediator Hubungan Industrial A.103', 'tgl_mulai' => '2026-09-01', 'tgl_selesai' => '2026-09-22', 'peserta' => 42],
                ]
            ],
            [
                'nama' => 'Pelatihan Dasar PPPK',
                'kode' => 'LATSAR-PPPK-2026',
                'angkatans' => [
                    ['nomor' => 'Latsar PPPK A.1', 'tgl_mulai' => '2026-11-01', 'tgl_selesai' => '2026-11-20', 'peserta' => 40],
                ]
            ],
            [
                'nama' => 'TOT PENGAMPU MATERI',
                'kode' => 'TOT-MATERI-2026',
                'angkatans' => [
                    ['nomor' => 'TOT A.1', 'tgl_mulai' => '2026-11-10', 'tgl_selesai' => '2026-11-15', 'peserta' => 25],
                ]
            ],
        ];

        foreach ($pelatihans as $pData) {
            $earliestStart = '2026-11-01';
            $latestEnd = '2026-11-30';
            if (!empty($pData['angkatans'])) {
                $earliestStart = $pData['angkatans'][0]['tgl_mulai'];
                $latestEnd = end($pData['angkatans'])['tgl_selesai'];
            }

            $pel = Pelatihan::firstOrCreate(
                ['nama_pelatihan' => $pData['nama']],
                [
                    'kode_pelatihan' => $pData['kode'],
                    'kategori' => 'Teknis',
                    'penyelenggara' => 'PPSDM Kemnaker (KCLC)',
                    'tanggal_mulai' => $earliestStart,
                    'tanggal_selesai' => $latestEnd,
                    'status' => 'Berjalan',
                    'keterangan' => 'Pelatihan Terintegrasi KCLC Kemnaker Corpu',
                ]
            );

            foreach ($pData['angkatans'] as $aData) {
                Angkatan::firstOrCreate(
                    [
                        'pelatihan_id' => $pel->id,
                        'nomor_angkatan' => $aData['nomor'],
                    ],
                    [
                        'tanggal_mulai' => $aData['tgl_mulai'],
                        'tanggal_selesai' => $aData['tgl_selesai'],
                        'jumlah_peserta' => $aData['peserta'],
                        'status' => 'Berjalan',
                    ]
                );
            }
        }

        // 3. Kelas Mapel & Jadwal Sesuai Gambar KCLC
        $pelAntarKerja = Pelatihan::where('nama_pelatihan', 'Pelatihan Teknis Antar Kerja PPPK Pengantar Kerja')->first();
        $angPPPK = Angkatan::where('nomor_angkatan', 'Pengantar Kerja PPPK')->first();

        $mapelCoaching = MataPelatihan::firstOrCreate(
            [
                'angkatan_id' => $angPPPK->id,
                'nama_mata_pelatihan' => 'Coaching',
            ],
            [
                'rencana_jp' => 4,
                'deskripsi' => 'Bimbingan dan coaching teknis pengantar kerja',
            ]
        );

        $kelasMapel = KelasMapel::firstOrCreate(
            [
                'pelatihan_id' => $pelAntarKerja->id,
                'angkatan_id' => $angPPPK->id,
                'nama_kelas' => 'PK PPPK',
            ],
            [
                'mata_pelatihan_id' => $mapelCoaching->id,
                'ruangan_id' => $ruangOnline?->id,
                'kapasitas' => 350,
                'kurikulum' => '2026',
                'tanggal_mulai' => '2026-11-25',
                'tanggal_selesai' => '2026-11-25',
                'jp' => 4,
                'ada_kelompok' => 'tidak',
                'hari' => 'Rabu',
                'jam_mulai' => '08:00',
                'jam_selesai' => '12:00',
                'metode_pembelajaran' => 'Synchronous',
            ]
        );

        // 4. Pengajar Kelas Mapel (Imam Syafi'i, S.E., M.T. - Narasumber / Widyaiswara)
        $pengajarImam = Narasumber::firstOrCreate(
            ['nama_lengkap' => "Imam Syafi'i, S.E., M.T."],
            [
                'nik' => '3171012301820005',
                'gelar_depan' => null,
                'gelar_belakang' => 'S.E., M.T.',
                'instansi' => 'Kementerian Ketenagakerjaan RI',
                'status' => 'Aktif',
            ]
        );

        KelasMapelPengajar::firstOrCreate(
            [
                'kelas_mapel_id' => $kelasMapel->id,
                'pengajar_type' => Narasumber::class,
                'pengajar_id' => $pengajarImam->id,
            ],
            [
                'jp' => 4,
                'jadwal_keterangan' => 'Rabu 08:00-12:00',
                'peran' => 'narasumber',
                'kelompok' => null,
            ]
        );

        // 5. Peserta Kelas (Presisi sesuai screenshot KCLC Tab 4)
        $pesertaData = [
            [
                'nip' => '1993102320212310008',
                'nama' => 'Zen Syaifi Hakiki',
                'email' => 'zen.hakiki@gmail.com',
                'jk' => 'Laki-laki',
                'instansi' => 'DIREKTORAT PENEMPATAN PEMERINTAH',
                'jabatan' => 'PENGANTAR KERJA AHLI PERTAMA',
                'gol' => 'IX',
            ],
            [
                'nip' => '198212302024211001',
                'nama' => 'Lagowe',
                'email' => 'lagowestg@gmail.com',
                'jk' => 'Laki-laki',
                'instansi' => 'Dinas Tenaga Kerja dan Transmigrasi Kalimantan Barat',
                'jabatan' => 'Pengantar Kerja Ahli Pertama',
                'gol' => 'IX',
            ],
            [
                'nip' => '199005012022211001',
                'nama' => 'Hamidun',
                'email' => 'hamidun2001@gmail.com',
                'jk' => 'Laki-laki',
                'instansi' => 'Dinas Tenaga Kerja dan Transmigrasi Sambas',
                'jabatan' => 'Pengantar Kerja Ahli Pertama',
                'gol' => 'IX',
            ],
            [
                'nip' => '198510232022211001',
                'nama' => 'Usmadi, S.E',
                'email' => 'usmadi85@gmail.com',
                'jk' => 'Laki-laki',
                'instansi' => 'Dinas Tenaga Kerja dan Transmigrasi Kabupaten Sambas',
                'jabatan' => 'Pengantar Kerja Ahli Pertama',
                'gol' => 'IX',
            ],
            [
                'nip' => '199810242023212015',
                'nama' => 'Annisa Fadillah, S.E',
                'email' => 'annisafadillah2500@gmail.com',
                'jk' => 'Perempuan',
                'instansi' => 'Balai Pelatihan Vokasi dan Produktivitas Padang',
                'jabatan' => 'Pengantar Kerja Ahli Pertama',
                'gol' => 'IX',
            ],
            [
                'nip' => '198810082024212029',
                'nama' => 'Wiwin Asmianti, S.I.P',
                'email' => 'wiwinasmianti5@gmail.com',
                'jk' => 'Perempuan',
                'instansi' => 'Dinas penanaman modal pelayanan terpadu satu pintu',
                'jabatan' => 'Pengantar Kerja Ahli Pertama',
                'gol' => 'IX',
            ],
            [
                'nip' => '198507212025211003',
                'nama' => 'Samuel Tandungan, S.E.',
                'email' => 'samueltandungan210785@gmail.com',
                'jk' => 'Laki-laki',
                'instansi' => 'Dinas Tenaga Kerja dan Transmigrasi Kabupaten Kola',
                'jabatan' => 'Pengantar Kerja Ahli Pertama',
                'gol' => 'IX',
            ],
            [
                'nip' => '198603232024212001',
                'nama' => 'Yustianingsih, S. Sos',
                'email' => 'izkiatitin@gmail.com',
                'jk' => 'Perempuan',
                'instansi' => 'Dinas Tenaga Kerja dan Transmigrasi Provinsi',
                'jabatan' => 'Pengantar Kerja Ahli Pertama',
                'gol' => 'IX',
            ],
            [
                'nip' => '198808092022212001',
                'nama' => 'Rahmah Gustiha, SE',
                'email' => 'rahma.gustiha@gmail.com',
                'jk' => 'Perempuan',
                'instansi' => 'Dinas Tenaga Kerja dan Transmigrasi Kabupaten Samb',
                'jabatan' => 'Pengantar Kerja Ahli Pertama',
                'gol' => 'IX',
            ],
            [
                'nip' => '198405102022212003',
                'nama' => 'Herlina, SE',
                'email' => 'sulienaoal84@gmail.com',
                'jk' => 'Perempuan',
                'instansi' => 'Dinas Tenaga Kerja dan Transmigrasi Kabupaten Samb',
                'jabatan' => 'Pengantar Kerja Ahli Pertama',
                'gol' => 'IX',
            ],
        ];

        foreach ($pesertaData as $p) {
            $peserta = Peserta::firstOrCreate(
                ['nip' => $p['nip']],
                [
                    'angkatan_id' => $angPPPK->id,
                    'kelas_mapel_id' => $kelasMapel->id,
                    'nama' => $p['nama'],
                    'email' => $p['email'],
                    'jenis_kelamin' => $p['jk'],
                    'instansi' => $p['instansi'],
                    'jabatan' => $p['jabatan'],
                    'golongan' => $p['gol'],
                ]
            );

            // Record Presensi Hadir
            Presensi::firstOrCreate(
                [
                    'peserta_id' => $peserta->id,
                    'kelas_mapel_id' => $kelasMapel->id,
                    'tanggal' => '2026-11-25',
                ],
                [
                    'status_kehadiran' => 'Hadir',
                    'waktu_presensi' => '07:55:00',
                ]
            );

            // Kode Akses & PIN Peserta Portal
            \App\Models\ParticipantAccessCode::firstOrCreate(
                ['peserta_id' => $peserta->id],
                [
                    'kode_peserta' => 'PST-2026-' . str_pad($peserta->id, 4, '0', STR_PAD_LEFT),
                    'pin_hash' => \Illuminate\Support\Facades\Hash::make('123456'),
                    'is_active' => true,
                ]
            );
        }

        // 6. Aspek Penilaian Sikap (Sesuai Gambar 6 & 7)
        $pelInstruktur = Pelatihan::where('nama_pelatihan', 'Pelatihan Pembentukan Jabatan Fungsional Instruktur')->first();
        $pelMHI = Pelatihan::where('nama_pelatihan', 'Pelatihan Dasar MHI')->first();
        $pelPPPK = Pelatihan::where('nama_pelatihan', 'Pelatihan Dasar PPPK')->first();
        $pelTOT = Pelatihan::where('nama_pelatihan', 'TOT PENGAMPU MATERI')->first();

        $sikapData = [
            [
                'pelatihan_id' => $pelInstruktur->id,
                'judul' => 'Penilaian Sikap Perilaku Peserta oleh Coach',
                'kategori' => 'Nilai Sikap dari Coach',
                'aspek' => ['Kedisiplinan', 'Kepemimpinan', 'Kerjasama', 'Prakarsa'],
            ],
            [
                'pelatihan_id' => $pelInstruktur->id,
                'judul' => 'Penilaian Sikap Perilaku Peserta Oleh Widyaiswara/Pengajar',
                'kategori' => 'Nilai Sikap dari Pengajar',
                'aspek' => ['Kedisiplinan', 'Keaktifan Bertanya', 'Kerjasama Kelompok'],
            ],
            [
                'pelatihan_id' => $pelInstruktur->id,
                'judul' => 'Penilaian Sikap Perilaku Peserta Oleh Panitia',
                'kategori' => 'Nilai Sikap dari Panitia',
                'aspek' => ['Ketepatan Waktu', 'Kerapihan Pakaian', 'Kepatuhan Tata Tertib'],
            ],
            [
                'pelatihan_id' => $pelMHI->id,
                'judul' => 'Penilaian Sikap Perilaku Peserta dari Panitia',
                'kategori' => 'Nilai Sikap dari Panitia',
                'aspek' => ['Kehadiran', 'Integritas'],
            ],
            [
                'pelatihan_id' => $pelMHI->id,
                'judul' => 'Penilaian Sikap Perilaku Peserta',
                'kategori' => 'Nilai Sikap Peserta',
                'aspek' => ['Etika Berkomunikasi', 'Kerjasama'],
            ],
            [
                'pelatihan_id' => $pelAntarKerja->id,
                'judul' => 'Instruktur',
                'kategori' => 'Nilai Sikap Instruktur',
                'aspek' => ['Kompetensi', 'Interaksi Belajar'],
            ],
            [
                'pelatihan_id' => $pelPPPK->id,
                'judul' => 'Antar Peserta',
                'kategori' => 'Nilai Sikap Antar Peserta',
                'aspek' => ['Kerjasama Tim', 'Toleransi'],
            ],
            [
                'pelatihan_id' => $pelPPPK->id,
                'judul' => 'dari Panitia',
                'kategori' => 'Nilai Sikap dari Panitia',
                'aspek' => ['Ketertiban', 'Kehadiran'],
            ],
            [
                'pelatihan_id' => $pelPPPK->id,
                'judul' => 'dari Pengajar',
                'kategori' => 'Nilai Sikap dari Pengajar',
                'aspek' => ['Partisipasi', 'Tugas Mandiri'],
            ],
            [
                'pelatihan_id' => $pelTOT->id,
                'judul' => 'nilai sikap antar peserta',
                'kategori' => 'Nilai Sikap Antar Peserta',
                'aspek' => ['Peer Review', 'Kolaborasi'],
            ],
        ];

        foreach ($sikapData as $s) {
            AspekPenilaianSikap::firstOrCreate(
                [
                    'pelatihan_id' => $s['pelatihan_id'],
                    'judul' => $s['judul'],
                ],
                [
                    'kategori' => $s['kategori'],
                    'aspek_penilaian' => $s['aspek'],
                    'is_active' => true,
                ]
            );
        }

        // 7. Form Evaluasi (Sesuai Gambar 8 & 9)
        $evaluasiData = [
            [
                'pelatihan_id' => $pelInstruktur->id,
                'judul' => 'Penilaian Widyaiswara/pengajar oleh Peserta',
                'deskripsi' => 'Formulir kuesioner evaluasi kompetensi pedagogik dan penguasaan materi pengajar oleh peserta diklat.',
            ],
            [
                'pelatihan_id' => $pelInstruktur->id,
                'judul' => 'Evaluasi Penyelenggaraan Pelatihan Fungsional',
                'deskripsi' => 'Evaluasi menyeluruh sarana, prasarana, LMS KCLC, dan pelayanan panitia penyelenggara.',
            ],
            [
                'pelatihan_id' => $pelInstruktur->id,
                'judul' => 'Penilaian Penguji/Evaluator oleh Peserta',
                'deskripsi' => 'Umpan balik objektivitas dan kejelasan penguji saat seminar/evaluasi akhir.',
            ],
            [
                'pelatihan_id' => $pelInstruktur->id,
                'judul' => 'Penilaian Mentor oleh Peserta',
                'deskripsi' => 'Bimbingan aksi perubahan dan aktualisasi di tempat kerja oleh mentor instansi.',
            ],
            [
                'pelatihan_id' => $pelInstruktur->id,
                'judul' => 'Penilaian Coach oleh Peserta',
                'deskripsi' => 'Efektivitas proses pendampingan coaching dan arahan penyusunan laporan.',
            ],
        ];

        foreach ($evaluasiData as $e) {
            FormEvaluasi::firstOrCreate(
                [
                    'pelatihan_id' => $e['pelatihan_id'],
                    'judul_form' => $e['judul'],
                ],
                [
                    'deskripsi' => $e['deskripsi'],
                    'is_active' => true,
                ]
            );
        }
    }
}
