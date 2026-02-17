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
        Schema::create('sub_kegiatans', function (Blueprint $table) {
            $table->id();

            $table->string('nomor_rekening');
            $table->string('nama_kegiatan');

            $table->foreignId('pegawai_kasubid_id')
                ->constrained('pegawais')
                ->cascadeOnDelete();

            $table->integer('koefisien');
            $table->bigInteger('pagu');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_kegiatans');
    }
};
