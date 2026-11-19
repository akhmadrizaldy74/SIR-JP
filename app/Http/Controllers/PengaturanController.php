<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\AturanJp;
use App\Models\PengaturanSesi;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $settings = Pengaturan::pluck('value', 'key')->toArray();
        $aturanJp = AturanJp::where('is_active', true)->first();
        $templateSesies = PengaturanSesi::orderBy('urutan', 'asc')->get();

        return view('pengaturan.index', compact('settings', 'aturanJp', 'templateSesies'));
    }

    public function updateUmum(Request $request)
    {
        $validated = $request->validate([
            'instansi_nama' => ['required', 'string', 'max:255'],
            'instansi_alamat' => ['nullable', 'string'],
            'instansi_telepon' => ['nullable', 'string', 'max:50'],
            'instansi_email' => ['nullable', 'email', 'max:255'],
            'pimpinan_nama' => ['required', 'string', 'max:255'],
            'pimpinan_nip' => ['nullable', 'string', 'max:50'],
            'pimpinan_jabatan' => ['required', 'string', 'max:255'],
            'pimpinan_kota' => ['required', 'string', 'max:100'],
            'minimum_jp_bulanan' => ['required', 'numeric', 'min:1'],
        ]);

        foreach ($validated as $key => $val) {
            Pengaturan::set($key, $val);
        }

        // Sinkronkan juga ke aturan_jps
        $aturan = AturanJp::first();
        if ($aturan) {
            $aturan->update([
                'minimum_jp_bulanan' => $validated['minimum_jp_bulanan'],
            ]);
        }

        LogAktivitas::catat('Pengaturan', 'Ubah Pengaturan Instansi', 'Memperbarui profil instansi dan batas minimum JP.');

        return back()->with('success', 'Pengaturan instansi dan kebijakan JP berhasil diperbarui.');
    }

    public function updateSesi(Request $request, PengaturanSesi $sesi)
    {
        $validated = $request->validate([
            'nama_sesi' => ['required', 'string', 'max:100'],
            'jam_mulai' => ['required'],
            'jam_selesai' => ['required'],
            'durasi_menit' => ['required', 'integer', 'min:1'],
            'jumlah_jp' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        $sesi->update($validated);

        LogAktivitas::catat('Pengaturan', 'Ubah Template Sesi', 'Memperbarui template sesi: ' . $sesi->nama_sesi);

        return back()->with('success', 'Template sesi pembelajaran berhasil diperbarui.');
    }
}
