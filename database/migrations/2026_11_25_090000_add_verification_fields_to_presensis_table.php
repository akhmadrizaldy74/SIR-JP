<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('presensis')) {
            Schema::table('presensis', function (Blueprint $table) {
                if (!Schema::hasColumn('presensis', 'status_verifikasi')) {
                    $table->string('status_verifikasi', 30)->default('Disetujui')->after('bukti_file');
                }
                if (!Schema::hasColumn('presensis', 'catatan_verifikasi')) {
                    $table->text('catatan_verifikasi')->nullable()->after('status_verifikasi');
                }
                if (!Schema::hasColumn('presensis', 'diverifikasi_oleh')) {
                    $table->foreignId('diverifikasi_oleh')->nullable()->after('catatan_verifikasi')->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('presensis', 'diverifikasi_pada')) {
                    $table->datetime('diverifikasi_pada')->nullable()->after('diverifikasi_oleh');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('presensis')) {
            Schema::table('presensis', function (Blueprint $table) {
                if (Schema::hasColumn('presensis', 'diverifikasi_oleh')) {
                    $table->dropForeign(['diverifikasi_oleh']);
                }
                $cols = array_filter([
                    'status_verifikasi',
                    'catatan_verifikasi',
                    'diverifikasi_oleh',
                    'diverifikasi_pada',
                ], fn ($col) => Schema::hasColumn('presensis', $col));

                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
