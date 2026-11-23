<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Jenis Pelatihan
        if (!Schema::hasTable('jenis_pelatihans')) {
            Schema::create('jenis_pelatihans', function (Blueprint $table) {
                $table->id();
                $table->string('nama_jenis');
                $table->boolean('status')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. Tabel Tahun Kurikulum
        if (!Schema::hasTable('tahun_kurikulums')) {
            Schema::create('tahun_kurikulums', function (Blueprint $table) {
                $table->id();
                $table->string('tahun');
                $table->boolean('status')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 3. Tambah kolom pelatihan_id & tahun_kurikulum ke mata_pelatihans
        Schema::table('mata_pelatihans', function (Blueprint $table) {
            if (!Schema::hasColumn('mata_pelatihans', 'pelatihan_id')) {
                $table->unsignedBigInteger('pelatihan_id')->nullable()->after('angkatan_id');
            }
            if (!Schema::hasColumn('mata_pelatihans', 'tahun_kurikulum')) {
                $table->string('tahun_kurikulum', 10)->default('2026')->after('rencana_jp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mata_pelatihans', function (Blueprint $table) {
            if (Schema::hasColumn('mata_pelatihans', 'tahun_kurikulum')) {
                $table->dropColumn('tahun_kurikulum');
            }
            if (Schema::hasColumn('mata_pelatihans', 'pelatihan_id')) {
                $table->dropColumn('pelatihan_id');
            }
        });

        Schema::dropIfExists('tahun_kurikulums');
        Schema::dropIfExists('jenis_pelatihans');
    }
};
