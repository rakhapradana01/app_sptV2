<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nota_dinas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sub_kegiatan_id')
                ->constrained('sub_kegiatans')
                ->cascadeOnDelete();

            $table->unsignedInteger('nomor_urut')->nullable();
            $table->year('tahun')->nullable();

            $table->date('tanggal');

            $table->foreignId('kepada_id')
                ->constrained('pegawais')
                ->cascadeOnDelete();

            $table->foreignId('melalui_id')
                ->nullable()
                ->constrained('pegawais')
                ->nullOnDelete();

            // TAMBAHAN
            $table->foreignId('dari_id')
                ->constrained('pegawais')
                ->cascadeOnDelete();

            $table->string('perihal');
            $table->string('kegiatan');
            $table->string('asal_undangan')->nullable();

            $table->string('lokasi');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai');

            $table->enum('status', [
                'draft',
                'diajukan_kasubid',
                'ditolak_kasubid',
                'diajukan_kabid',
                'ditolak_kabid',
                'disetujui_kabid',
                'diajukan_kaban',
                'ditolak_kaban',
                'disetujui_kaban',
            ])->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_dinas');
    }
};
