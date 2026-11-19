<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelatihans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pelatihan', 50)->unique();
            $table->string('nama_pelatihan');
            $table->string('kategori')->default('Teknis');
            $table->string('penyelenggara')->default('PPSDM');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status', ['Direncanakan', 'Berjalan', 'Selesai', 'Dibatalkan'])->default('Direncanakan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelatihans');
    }
};
