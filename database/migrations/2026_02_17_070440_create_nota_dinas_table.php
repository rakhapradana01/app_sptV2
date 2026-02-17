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
        Schema::create('nota_dinas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->text('kegiatan');

            $table->foreignId('pegawai_kasubid_id')
                ->constrained('pegawais')
                ->cascadeOnDelete();

            $table->foreignId('pegawai_kabid_id')
                ->constrained('pegawais')
                ->cascadeOnDelete();

            $table->foreignId('pegawai_kaban_id')
                ->constrained('pegawais')
                ->cascadeOnDelete();
                
            $table->foreignId('sub_kegiatan_id')
                ->constrained('sub_kegiatans')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_dinas');
    }
};
