<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penugasan_pengajars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelatihan_id')->constrained('mata_pelatihans')->cascadeOnDelete();
            $table->enum('jenis_pengajar', ['Widyaiswara', 'Narasumber']);
            $table->foreignId('widyaiswara_id')->nullable()->constrained('widyaiswaras')->nullOnDelete();
            $table->foreignId('narasumber_id')->nullable()->constrained('narasumbers')->nullOnDelete();
            $table->string('peran')->default('Pengampu Utama');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['mata_pelatihan_id', 'jenis_pengajar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan_pengajars');
    }
};
