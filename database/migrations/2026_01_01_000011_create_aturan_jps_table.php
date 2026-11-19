<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aturan_jps', function (Blueprint $table) {
            $table->id();
            $table->string('nama_aturan')->default('Standar Minimum JP Widyaiswara PPSDM');
            $table->decimal('minimum_jp_bulanan', 5, 2)->default(32.00);
            $table->unsignedInteger('durasi_per_jp_menit')->default(45);
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aturan_jps');
    }
};
