<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sppds', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('tanggal_sppd');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
            $table->text('kegiatan')->nullable()->after('tanggal_selesai');
            $table->string('nomor_spt_ref')->nullable()->after('kegiatan');
        });
    }

    public function down(): void
    {
        Schema::table('sppds', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_mulai',
                'tanggal_selesai',
                'kegiatan',
                'nomor_spt_ref',
            ]);
        });
    }
};
