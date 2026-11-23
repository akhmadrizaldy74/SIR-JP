<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('presensis')) {
            Schema::table('presensis', function (Blueprint $table) {
                if (!Schema::hasColumn('presensis', 'bukti_file')) {
                    $table->string('bukti_file')->nullable()->after('keterangan');
                }
            });

            DB::statement("ALTER TABLE presensis MODIFY COLUMN metode_presensi VARCHAR(30) NOT NULL DEFAULT 'Manual'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('presensis')) {
            Schema::table('presensis', function (Blueprint $table) {
                if (Schema::hasColumn('presensis', 'bukti_file')) {
                    $table->dropColumn('bukti_file');
                }
            });
        }
    }
};
