<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Widyaiswara;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginationResponsiveTest extends TestCase
{
    public function test_pagination_renders_clean_bootstrap5_without_raw_keys_or_svg(): void
    {
        $user = User::first() ?? User::factory()->create();

        // Ensure we have enough widyaiswara to paginate (at least 15 items with perPage 10)
        $count = Widyaiswara::count();
        if ($count < 15) {
            for ($i = $count + 1; $i <= 15; $i++) {
                Widyaiswara::create([
                    'nip' => '198001012005011' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'nama_lengkap' => 'WI Test ' . $i,
                    'jabatan' => 'Widyaiswara Ahli Madya',
                    'pangkat_golongan' => 'IV/a',
                    'status_kepegawaian' => 'PNS',
                    'target_jp_tahunan' => 384,
                ]);
            }
        }

        $response = $this->actingAs($user)->get(route('widyaiswara.index'));

        $response->assertStatus(200);

        // Check that raw pagination keys do NOT appear
        $response->assertDontSee('pagination.previous');
        $response->assertDontSee('pagination.next');
        $response->assertDontSee('Showing 1 to 10 of');

        // Check that proper Indonesian count and clean pagination nav appear
        $response->assertSee('Menampilkan 1 - 10 dari');
        $response->assertSee('table-pagination-footer');
        $response->assertSee('class="pagination pagination-sm', false);
        $response->assertSee('aria-label="Sebelumnya"', false);
        $response->assertSee('aria-label="Selanjutnya"', false);
    }
}
