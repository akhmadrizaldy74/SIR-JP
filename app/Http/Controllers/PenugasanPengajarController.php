<?php

namespace App\Http\Controllers;

use App\Models\PenugasanPengajar;
use App\Models\Pelatihan;
use App\Models\Angkatan;
use App\Models\MataPelatihan;
use App\Models\Widyaiswara;
use App\Models\Narasumber;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PenugasanPengajarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $jenisPengajar = $request->get('jenis_pengajar');
        $pelatihanId = $request->get('pelatihan_id');

        $query = PenugasanPengajar::with(['mataPelatihan.angkatan.pelatihan', 'widyaiswara', 'narasumber']);

        if ($jenisPengajar) {
            $query->where('jenis_pengajar', $jenisPengajar);
        }

        if ($pelatihanId) {
            $query->whereHas('mataPelatihan.angkatan', function ($q) use ($pelatihanId) {
                $q->where('pelatihan_id', $pelatihanId);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('widyaiswara', function ($w) use ($search) {
                    $w->where('nama_lengkap', 'like', "%{$search}%");
                })->orWhereHas('narasumber', function ($n) use ($search) {
                    $n->where('nama_lengkap', 'like', "%{$search}%");
                })->orWhereHas('mataPelatihan', function ($m) use ($search) {
                    $m->where('nama_mata_pelatihan', 'like', "%{$search}%");
                });
            });
        }

        $penugasans = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $pelatihans = Pelatihan::orderBy('nama_pelatihan', 'asc')->get();

        return view('penugasan.index', compact('penugasans', 'pelatihans', 'search', 'jenisPengajar', 'pelatihanId'));
    }

    public function create(Request $request)
    {
        $pelatihans = Pelatihan::with('angkatans.mataPelatihans')->orderBy('nama_pelatihan', 'asc')->get();
        $widyaiswaras = Widyaiswara::where('status', 'Aktif')->orderBy('nama_lengkap', 'asc')->get();
        $narasumbers = Narasumber::where('status', 'Aktif')->orderBy('nama_lengkap', 'asc')->get();

        $selectedMataPelatihanId = $request->get('mata_pelatihan_id');
        $selectedMataPelatihan = $selectedMataPelatihanId ? MataPelatihan::with('angkatan.pelatihan')->find($selectedMataPelatihanId) : null;

        return view('penugasan.create', compact('pelatihans', 'widyaiswaras', 'narasumbers', 'selectedMataPelatihanId', 'selectedMataPelatihan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_pelatihan_id' => ['required', 'exists:mata_pelatihans,id'],
            'jenis_pengajar' => ['required', Rule::in(['Widyaiswara', 'Narasumber'])],
            'widyaiswara_id' => ['nullable', 'required_if:jenis_pengajar,Widyaiswara', 'exists:widyaiswaras,id'],
            'narasumber_id' => ['nullable', 'required_if:jenis_pengajar,Narasumber', 'exists:narasumbers,id'],
            'peran' => ['required', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'mata_pelatihan_id.required' => 'Mata pelatihan wajib dipilih.',
            'jenis_pengajar.required' => 'Jenis pengajar wajib dipilih.',
            'widyaiswara_id.required_if' => 'Silakan pilih Widyaiswara.',
            'narasumber_id.required_if' => 'Silakan pilih Narasumber.',
            'peran.required' => 'Peran penugasan wajib diisi.',
        ]);

        // Cegah penugasan ganda pengajar yang sama pada mata pelatihan yang sama
        $duplicateCheck = PenugasanPengajar::where('mata_pelatihan_id', $validated['mata_pelatihan_id'])
            ->where('jenis_pengajar', $validated['jenis_pengajar'])
            ->where(function ($q) use ($validated) {
                if ($validated['jenis_pengajar'] === 'Widyaiswara') {
                    $q->where('widyaiswara_id', $validated['widyaiswara_id']);
                } else {
                    $q->where('narasumber_id', $validated['narasumber_id']);
                }
            })
            ->first();

        if ($duplicateCheck) {
            return back()->withInput()->with('error', 'Pengajar ini sudah ditugaskan pada mata pelatihan yang dipilih.');
        }

        $penugasan = PenugasanPengajar::create([
            'mata_pelatihan_id' => $validated['mata_pelatihan_id'],
            'jenis_pengajar' => $validated['jenis_pengajar'],
            'widyaiswara_id' => $validated['jenis_pengajar'] === 'Widyaiswara' ? $validated['widyaiswara_id'] : null,
            'narasumber_id' => $validated['jenis_pengajar'] === 'Narasumber' ? $validated['narasumber_id'] : null,
            'peran' => $validated['peran'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        LogAktivitas::catat('Penugasan Pengajar', 'Tambah Penugasan', 'Menugaskan ' . $penugasan->pengajar_nama . ' pada ' . $penugasan->mataPelatihan->nama_mata_pelatihan);

        return redirect()->route('penugasan.index')->with('success', 'Penugasan pengajar berhasil disimpan.');
    }

    public function edit(PenugasanPengajar $penugasan)
    {
        $penugasan->load(['mataPelatihan.angkatan.pelatihan']);
        $widyaiswaras = Widyaiswara::where('status', 'Aktif')->orderBy('nama_lengkap', 'asc')->get();
        $narasumbers = Narasumber::where('status', 'Aktif')->orderBy('nama_lengkap', 'asc')->get();

        return view('penugasan.edit', compact('penugasan', 'widyaiswaras', 'narasumbers'));
    }

    public function update(Request $request, PenugasanPengajar $penugasan)
    {
        $validated = $request->validate([
            'jenis_pengajar' => ['required', Rule::in(['Widyaiswara', 'Narasumber'])],
            'widyaiswara_id' => ['nullable', 'required_if:jenis_pengajar,Widyaiswara', 'exists:widyaiswaras,id'],
            'narasumber_id' => ['nullable', 'required_if:jenis_pengajar,Narasumber', 'exists:narasumbers,id'],
            'peran' => ['required', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string'],
        ]);

        // Cek duplikasi
        $duplicateCheck = PenugasanPengajar::where('mata_pelatihan_id', $penugasan->mata_pelatihan_id)
            ->where('id', '!=', $penugasan->id)
            ->where('jenis_pengajar', $validated['jenis_pengajar'])
            ->where(function ($q) use ($validated) {
                if ($validated['jenis_pengajar'] === 'Widyaiswara') {
                    $q->where('widyaiswara_id', $validated['widyaiswara_id']);
                } else {
                    $q->where('narasumber_id', $validated['narasumber_id']);
                }
            })
            ->first();

        if ($duplicateCheck) {
            return back()->withInput()->with('error', 'Pengajar ini sudah ditugaskan pada mata pelatihan yang sama.');
        }

        $penugasan->update([
            'jenis_pengajar' => $validated['jenis_pengajar'],
            'widyaiswara_id' => $validated['jenis_pengajar'] === 'Widyaiswara' ? $validated['widyaiswara_id'] : null,
            'narasumber_id' => $validated['jenis_pengajar'] === 'Narasumber' ? $validated['narasumber_id'] : null,
            'peran' => $validated['peran'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        LogAktivitas::catat('Penugasan Pengajar', 'Ubah Penugasan', 'Memperbarui penugasan ' . $penugasan->pengajar_nama);

        return redirect()->route('penugasan.index')->with('success', 'Data penugasan pengajar berhasil diperbarui.');
    }

    public function destroy(PenugasanPengajar $penugasan)
    {
        $nama = $penugasan->pengajar_nama;
        $mp = $penugasan->mataPelatihan->nama_mata_pelatihan;
        $penugasan->delete();

        LogAktivitas::catat('Penugasan Pengajar', 'Hapus Penugasan', 'Membatalkan penugasan ' . $nama . ' pada ' . $mp);

        return redirect()->route('penugasan.index')->with('success', 'Penugasan pengajar berhasil dibatalkan.');
    }

    /**
     * Endpoint API JSON untuk cascading dropdown (Program -> Angkatan -> Mata Pelatihan)
     */
    public function getAngkatansByPelatihan($pelatihanId)
    {
        $angkatans = Angkatan::where('pelatihan_id', $pelatihanId)
            ->select('id', 'nomor_angkatan')
            ->get();
        return response()->json($angkatans);
    }

    public function getMataPelatihansByAngkatan($angkatanId)
    {
        $mataPelatihans = MataPelatihan::where('angkatan_id', $angkatanId)
            ->select('id', 'nama_mata_pelatihan', 'rencana_jp')
            ->get();
        return response()->json($mataPelatihans);
    }
}
