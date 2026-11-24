<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelatihan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pelatihans';

    protected $fillable = [
        'kode_pelatihan',
        'nama_pelatihan',
        'kategori',
        'penyelenggara',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'keterangan',
        'bank_soal_data',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'bank_soal_data' => 'array',
        ];
    }

    /**
     * Dapatkan daftar butir soal pre-test, kuis, dan post-test.
     */
    public function getBankSoalItems(): array
    {
        if (is_array($this->bank_soal_data) && count($this->bank_soal_data) > 0) {
            return $this->bank_soal_data;
        }

        // Butir soal default jika belum pernah disimpan/diubah
        return [
            [
                'id' => 1,
                'tipe' => 'pretest',
                'pertanyaan' => 'Apa prinsip utama dalam pelaksanaan tata kelola kepemimpinan dan pelayanan ketenagakerjaan berbasis Corporate University?',
                'pilihan' => [
                    'A' => 'Sentralisasi seluruh keputusan teknis tanpa delegasi',
                    'B' => 'Integrasi pembelajaran terpadu selaras dengan tujuan strategis organisasi',
                    'C' => 'Mengurangi jam pelatihan pegawai aparatur sipil negara',
                    'D' => 'Pembatasan akses informasi antar unit kerja',
                ],
                'kunci' => 'B',
                'bobot' => 10,
            ],
            [
                'id' => 2,
                'tipe' => 'pretest',
                'pertanyaan' => 'Dalam sistem evaluasi pembelajaran Kirkpatrick, level berapakah yang mengukur perubahan perilaku kerja nyata peserta di tempat tugas?',
                'pilihan' => [
                    'A' => 'Level 1 - Reaction',
                    'B' => 'Level 2 - Learning',
                    'C' => 'Level 3 - Behavior',
                    'D' => 'Level 4 - Results',
                ],
                'kunci' => 'C',
                'bobot' => 10,
            ],
            [
                'id' => 3,
                'tipe' => 'kuis',
                'pertanyaan' => 'Landasan hukum pengawasan ketenagakerjaan di Indonesia termuat dalam regulasi:',
                'pilihan' => [
                    'A' => 'Undang-Undang Nomor 3 Tahun 1951',
                    'B' => 'Undang-Undang Nomor 1 Tahun 1970',
                    'C' => 'Peraturan Pemerintah Nomor 35 Tahun 2021',
                    'D' => 'Permenaker Nomor 5 Tahun 2021',
                ],
                'kunci' => 'A',
                'bobot' => 10,
            ],
            [
                'id' => 4,
                'tipe' => 'posttest',
                'pertanyaan' => 'Indikator keberhasilan dari penyelenggaraan diklat aparatur yang berdampak tinggi adalah:',
                'pilihan' => [
                    'A' => 'Tingkat kehadiran peserta 100% tanpa evaluasi lanjut',
                    'B' => 'Banyaknya materi yang disampaikan dalam slide presentasi',
                    'C' => 'Jumlah anggaran yang terserap habis',
                    'D' => 'Peningkatan kinerja pelayanan publik dan capaian target strategis instansi',
                ],
                'kunci' => 'D',
                'bobot' => 20,
            ],
        ];
    }

    public function angkatans()
    {
        return $this->hasMany(Angkatan::class);
    }

    public function aspekPenilaianSikaps()
    {
        return $this->hasMany(AspekPenilaianSikap::class, 'pelatihan_id');
    }

    public function formEvaluasis()
    {
        return $this->hasMany(FormEvaluasi::class, 'pelatihan_id');
    }

    public function kelasMapels()
    {
        return $this->hasMany(KelasMapel::class, 'pelatihan_id');
    }
}
