<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->foreignId('kelas_mapel_id')
                ->nullable()
                ->after('mata_pelatihan_id')
                ->constrained('kelas_mapels')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropForeign(['kelas_mapel_id']);
            $table->dropColumn('kelas_mapel_id');
        });
    }
};
