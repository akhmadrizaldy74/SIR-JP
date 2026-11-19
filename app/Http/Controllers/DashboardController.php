<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JPRecapitulationService;
use App\Models\LogAktivitas;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected JPRecapitulationService $jpService;

    public function __construct(JPRecapitulationService $jpService)
    {
        $this->jpService = $jpService;
    }

    public function index(Request $request)
    {
        $bulan = (int) $request->get('bulan', Carbon::now()->month);
        $tahun = (int) $request->get('tahun', Carbon::now()->year);

        $stats = $this->jpService->getDashboardStats($bulan, $tahun);

        $recentActivities = LogAktivitas::with('user')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $namaBulan = $this->jpService->getNamaBulan($bulan);

        return view('dashboard.index', compact('stats', 'bulan', 'tahun', 'namaBulan', 'recentActivities'));
    }
}
