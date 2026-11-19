<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('angkatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelatihan_id')->constrained('pelatihans')->cascadeOnDelete();
            $table->string('nomor_angkatan'); // e.g. Angkatan I, Angkatan II, dsb.
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->unsignedInteger('jumlah_peserta')->default(0);
            $table->string('lokasi')->nullable();
            $table->enum('status', ['Direncanakan', 'Berjalan', 'Selesai', 'Dibatalkan'])->default('Direncanakan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('angkatans');
    }
};
