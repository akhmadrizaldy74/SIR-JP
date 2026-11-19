<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_sesies', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sesi');
            $table->enum('tipe', ['Pembelajaran', 'Istirahat', 'Ishoma'])->default('Pembelajaran');
            $table->unsignedInteger('nomor_sesi')->nullable();
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->unsignedInteger('durasi_menit');
            $table->decimal('jumlah_jp', 4, 2)->default(1.00);
            $table->unsignedInteger('urutan')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_sesies');
    }
};
