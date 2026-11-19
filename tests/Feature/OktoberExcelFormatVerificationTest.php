<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Widyaiswara;
use App\Services\JPRecapitulationService;
use App\Exports\RekapSemuaWidyaiswaraExport;
use App\Exports\WidyaiswaraIndividuExport;
use Maatwebsite\Excel\Facades\Excel;

class OktoberExcelFormatVerificationTest extends TestCase
{
    protected User $user;
    protected JPRecapitulationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::first();
        $this->service = new JPRecapitulationService();
    }

    public function test_oktober_data_matches_excel_source(): void
    {
        $faizal = Widyaiswara::where('nama_lengkap', 'like', '%Faizal Haqqi%')->first();
        $this->assertNotNull($faizal);

        $rekapFaizal = $this->service->hitungWidyaiswara($faizal->id, 10, 2026);
        $this->assertEquals(63.0, $rekapFaizal['total_a']);
        $this->assertEquals(0.0, $rekapFaizal['total_b']);
        $this->assertEquals(63.0, $rekapFaizal['total_ab']);
        $this->assertEquals(31.0, $rekapFaizal['total_dibayarkan']);
        $this->assertEquals('Melebihi', $rekapFaizal['status_pencapaian']);

        $renny = Widyaiswara::where('nama_lengkap', 'like', '%Renny Retnowatie%')->first();
        $this->assertNotNull($renny);
        $rekapRenny = $this->service->hitungWidyaiswara($renny->id, 10, 2026);
        $this->assertEquals(29.0, $rekapRenny['total_a']);
        $this->assertEquals(0.0, $rekapRenny['total_dibayarkan']);
        $this->assertEquals('Belum Memenuhi', $rekapRenny['status_pencapaian']);

        $adriani = Widyaiswara::where('nama_lengkap', 'like', '%Adriani%')->first();
        $this->assertNotNull($adriani);
        $rekapAdriani = $this->service->hitungWidyaiswara($adriani->id, 10, 2026);
        $this->assertEquals(82.0, $rekapAdriani['total_a']);
        $this->assertEquals(50.0, $rekapAdriani['total_dibayarkan']);
        $this->assertEquals('Melebihi', $rekapAdriani['status_pencapaian']);

        $alfiah = Widyaiswara::where('nama_lengkap', 'like', '%Alfiah Pra Mundiarsih%')->first();
        $this->assertNotNull($alfiah);
        $rekapAlfiah = $this->service->hitungWidyaiswara($alfiah->id, 10, 2026);
        $this->assertEquals(102.0, $rekapAlfiah['total_a']);
        $this->assertEquals(70.0, $rekapAlfiah['total_dibayarkan']);
        $this->assertEquals('Melebihi', $rekapAlfiah['status_pencapaian']);
    }

    public function test_rekap_semua_widyaiswara_generates_multiple_sheets(): void
    {
        $rekap = $this->service->rekapSemuaWidyaiswara(10, 2026);
        $export = new RekapSemuaWidyaiswaraExport($rekap, 10, 2026, 'Oktober', 32.0);

        $sheets = $export->sheets();
        $this->assertGreaterThan(10, count($sheets));

        // First sheet is master summary
        $this->assertEquals('REKAPITULASI', $sheets[0]->title());

        // Subsequent sheets are individual Widyaiswara sheets
        $this->assertInstanceOf(WidyaiswaraIndividuExport::class, $sheets[1]);
    }
}
