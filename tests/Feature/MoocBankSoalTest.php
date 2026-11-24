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

    public function test_admin_can_add_update_and_delete_butir_soal(): void
    {
        $pelatihan = Pelatihan::create([
            'nama_pelatihan' => 'Pelatihan Uji Butir Soal ' . rand(1000, 9999),
            'kode_pelatihan' => 'BUTIR-' . uniqid(),
            'status' => 'Berjalan',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(30)->toDateString(),
        ]);

        // 1. Tambah butir soal baru
        $response = $this->actingAs($this->admin)->post(route('mooc.bank-soal.butir.add', $pelatihan->id), [
            'tipe' => 'pretest',
            'pertanyaan' => 'Pertanyaan Baru Uji Coba K3?',
            'pilihan_a' => 'Opsi A Benar Sekali',
            'pilihan_b' => 'Opsi B',
            'pilihan_c' => 'Opsi C',
            'pilihan_d' => 'Opsi D',
            'kunci' => 'A',
            'bobot' => 15,
        ]);

        $response->assertRedirect(route('mooc.bank-soal'));
        $pelatihan->refresh();
        $items = $pelatihan->getBankSoalItems();
        $added = collect($items)->firstWhere('pertanyaan', 'Pertanyaan Baru Uji Coba K3?');
        $this->assertNotNull($added);
        $this->assertEquals('A', $added['kunci']);
        $this->assertEquals(15, $added['bobot']);

        // 2. Ubah / edit butir soal
        $responseEdit = $this->actingAs($this->admin)->put(route('mooc.bank-soal.butir.update', [
            'id' => $pelatihan->id,
            'soalId' => $added['id'],
        ]), [
            'tipe' => 'pretest',
            'pertanyaan' => 'Pertanyaan Sesudah Diedit Revisi?',
            'pilihan_a' => 'Opsi A',
            'pilihan_b' => 'Opsi B Baru',
            'pilihan_c' => 'Opsi C',
            'pilihan_d' => 'Opsi D',
            'kunci' => 'B',
            'bobot' => 25,
        ]);

        $responseEdit->assertRedirect(route('mooc.bank-soal'));
        $pelatihan->refresh();
        $updated = collect($pelatihan->getBankSoalItems())->firstWhere('id', $added['id']);
        $this->assertEquals('Pertanyaan Sesudah Diedit Revisi?', $updated['pertanyaan']);
        $this->assertEquals('B', $updated['kunci']);
        $this->assertEquals(25, $updated['bobot']);

        // 3. Hapus butir soal
        $responseDelete = $this->actingAs($this->admin)->delete(route('mooc.bank-soal.butir.delete', [
            'id' => $pelatihan->id,
            'soalId' => $added['id'],
        ]));

        $responseDelete->assertRedirect(route('mooc.bank-soal'));
        $pelatihan->refresh();
        $deleted = collect($pelatihan->getBankSoalItems())->firstWhere('id', $added['id']);
        $this->assertNull($deleted);
    }
}
