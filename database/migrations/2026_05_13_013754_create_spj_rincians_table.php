<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('spj_rincians', function (Blueprint $table) {
            $table->id();
            $table->string('kode_rekening')->nullable();
            $table->foreignId('nota_dinas_id')->constrained('nota_dinas')->onDelete('cascade');
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');
            $table->integer('jumlah_hari')->default(0);
            $table->integer('uang_harian')->default(0);
            $table->integer('tiket_pesawat_pergi')->default(0);
            $table->integer('tiket_pesawat_pulang')->default(0);
            $table->integer('transport')->default(0);
            $table->integer('total')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spj_rincians');
    }
};
