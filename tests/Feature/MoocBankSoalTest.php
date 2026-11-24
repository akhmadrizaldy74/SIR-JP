<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Pelatihan;
use Tests\TestCase;

class MoocBankSoalTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'roles' => ['Administrator', 'Pengajar'],
        ]);
    }

    public function test_admin_can_view_bank_soal_page(): void
    {
        $pelatihan = Pelatihan::create([
            'nama_pelatihan' => 'Pelatihan Uji Bank Soal ' . rand(1000, 9999),
            'kode_pelatihan' => 'TEST-BS-' . uniqid(),
            'status' => 'Berjalan',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(30)->toDateString(),
            'keterangan' => 'Paket Soal Uji Coba',
        ]);

        $response = $this->actingAs($this->admin)->get(route('mooc.bank-soal'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Bank Soal &amp; Kuis', false);
        $response->assertSee($pelatihan->nama_pelatihan);
        $response->assertSee('editBankSoalModal-' . $pelatihan->id);
    }

    public function test_admin_can_create_bank_soal_package(): void
    {
        $namaUnik = 'Pelatihan Investigasi Insiden K3 MOOC ' . rand(1000, 9999);
        $response = $this->actingAs($this->admin)->post(route('mooc.bank-soal.store'), [
            'nama_pelatihan' => $namaUnik,
            'kategori' => 'K3 & Investigasi',
            'status' => 'Aktif',
            'keterangan' => 'Bank soal pre-test dan evaluasi modul insiden.',
        ]);

        $response->assertRedirect(route('mooc.bank-soal'));
        $this->assertDatabaseHas('pelatihans', [
            'nama_pelatihan' => $namaUnik,
            'status' => 'Berjalan',
        ]);
    }

    public function test_admin_can_update_bank_soal_package(): void
    {
        $pelatihan = Pelatihan::create([
            'nama_pelatihan' => 'Pelatihan Lama Sebelum Edit ' . rand(1000, 9999),
            'kode_pelatihan' => 'OLD-BS-' . uniqid(),
            'status' => 'Berjalan',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(30)->toDateString(),
            'keterangan' => 'Deskripsi lama',
        ]);

        $namaBaru = 'Pelatihan Baru Sesudah Diedit ' . rand(1000, 9999);
        $response = $this->actingAs($this->admin)->put(route('mooc.bank-soal.update', $pelatihan->id), [
            'nama_pelatihan' => $namaBaru,
            'kategori' => 'Kepemimpinan',
            'status' => 'Aktif',
            'keterangan' => 'Deskripsi baru yang telah diubah.',
        ]);

        $response->assertRedirect(route('mooc.bank-soal'));
        $this->assertDatabaseHas('pelatihans', [
            'id' => $pelatihan->id,
            'nama_pelatihan' => $namaBaru,
            'keterangan' => 'Deskripsi baru yang telah diubah.',
        ]);
    }

    public function test_admin_can_delete_bank_soal_package(): void
    {
        $pelatihan = Pelatihan::create([
            'nama_pelatihan' => 'Pelatihan Untuk Dihapus ' . rand(1000, 9999),
            'kode_pelatihan' => 'DEL-BS-' . uniqid(),
            'status' => 'Berjalan',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(30)->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('mooc.bank-soal.destroy', $pelatihan->id));
        $response->assertRedirect(route('mooc.bank-soal'));
        $this->assertSoftDeleted('pelatihans', [
            'id' => $pelatihan->id,
        ]);
    }

    public function test_admin_can_filter_and_sort_bank_soal(): void
    {
        $uniqueKeyword = 'KhususFilterXYZ' . rand(100, 999);
        Pelatihan::create([
            'nama_pelatihan' => 'Pelatihan ' . $uniqueKeyword,
            'kode_pelatihan' => 'KEY-' . uniqid(),
            'status' => 'Berjalan',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(30)->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('mooc.bank-soal', [
            'search' => $uniqueKeyword,
            'status' => 'aktif',
            'sort' => 'name_asc',
        ]));

        $response->assertStatus(200);
        $response->assertSee($uniqueKeyword);
    }
}
