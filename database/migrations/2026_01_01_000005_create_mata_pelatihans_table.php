<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_pelatihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('angkatan_id')->constrained('angkatans')->cascadeOnDelete();
            $table->string('nama_mata_pelatihan');
            $table->decimal('rencana_jp', 5, 2)->default(1.00);
            $table->text('deskripsi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_pelatihans');
    }
};
