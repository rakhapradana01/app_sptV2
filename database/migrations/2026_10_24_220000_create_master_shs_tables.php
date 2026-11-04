<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel utama: satu baris = satu lokasi tujuan + tahun anggaran
        Schema::create('master_shs', function (Blueprint $table) {
            $table->id();
            $table->string('lokasi_tujuan');
            $table->year('tahun_anggaran');
            $table->decimal('uang_harian', 15, 2)->default(0)->comment('Pagu maks uang harian per hari');
            $table->decimal('tiket_pesawat', 15, 2)->default(0)->comment('Pagu maks tiket pesawat PP');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Cegah duplikasi lokasi + tahun
            $table->unique(['lokasi_tujuan', 'tahun_anggaran'], 'uq_lokasi_tahun');
        });

        // Detail penginapan: banyak tarif per golongan/kelas jabatan untuk satu lokasi
        Schema::create('master_shs_penginapan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_shs_id')->constrained('master_shs')->cascadeOnDelete();
            $table->string('golongan_kelas')->comment('Golongan/kelas jabatan, misal: Gol IV, Eselon II, dsb.');
            $table->decimal('tarif', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['master_shs_id', 'golongan_kelas'], 'uq_shs_penginapan');
        });

        // Detail transportasi: banyak tarif per rute untuk satu lokasi
        Schema::create('master_shs_transportasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_shs_id')->constrained('master_shs')->cascadeOnDelete();
            $table->string('rute')->comment('Rute transportasi, misal: Bandara - Hotel, Terminal - Kantor, dsb.');
            $table->string('jenis_transportasi')->nullable()->comment('Jenis kendaraan: taksi, bus, dsb.');
            $table->decimal('tarif', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['master_shs_id', 'rute', 'jenis_transportasi'], 'uq_shs_transportasi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_shs_transportasi');
        Schema::dropIfExists('master_shs_penginapan');
        Schema::dropIfExists('master_shs');
    }
};
