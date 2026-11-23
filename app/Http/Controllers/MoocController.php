<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelatihan;
use App\Models\Angkatan;
use App\Models\Peserta;

class MoocController extends Controller
{
    /**
     * Halaman Modul LMS MOOC - Pelatihan
     */
    public function pelatihan(Request $request)
    {
        $pelatihans = Pelatihan::withCount('angkatans')->paginate(10);
        return view('mooc.pelatihan', compact('pelatihans'));
    }

    /**
     * Halaman Modul LMS MOOC - Bank Soal
     */
    public function bankSoal(Request $request)
    {
        $pelatihans = Pelatihan::all();
        return view('mooc.bank-soal', compact('pelatihans'));
    }

    /**
     * Halaman Modul LMS MOOC - Riwayat Pelatihan Peserta
     */
    public function riwayat(Request $request)
    {
        $pesertas = Peserta::with('angkatan.pelatihan')->paginate(15);
        return view('mooc.riwayat', compact('pesertas'));
    }

    /**
     * Halaman Modul LMS MOOC - Komentar Pelatihan
     */
    public function komentar(Request $request)
    {
        return view('mooc.komentar');
    }

    /**
     * Halaman Modul LMS MOOC - Nilai Peserta Ujian Permodul
     */
    public function nilaiUjian(Request $request)
    {
        $pesertas = Peserta::with('angkatan.pelatihan')->paginate(15);
        return view('mooc.nilai-ujian', compact('pesertas'));
    }
}
