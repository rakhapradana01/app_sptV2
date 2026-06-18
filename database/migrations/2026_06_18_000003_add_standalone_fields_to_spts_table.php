<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spts', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('jenis_anggaran');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
            $table->string('lokasi')->nullable()->after('tanggal_selesai');
            $table->text('kegiatan')->nullable()->after('lokasi');
            $table->foreignId('sub_kegiatan_id')
                ->nullable()
                ->after('kegiatan')
                ->constrained('sub_kegiatans')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('spts', function (Blueprint $table) {
            $table->dropForeign(['sub_kegiatan_id']);
            $table->dropColumn([
                'tanggal_mulai',
                'tanggal_selesai',
                'lokasi',
                'kegiatan',
                'sub_kegiatan_id',
            ]);
        });
    }
};
