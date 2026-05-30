<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ubah tanggal_mulai menjadi NOT NULL karena selalu wajib diisi.
     */
    public function up(): void
    {
        // Pastikan data lama yang NULL diisi dulu sebelum ubah constraint
        \DB::table('nota_dinas')
            ->whereNull('tanggal_mulai')
            ->update(['tanggal_mulai' => \DB::raw('tanggal')]);

        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable(false)->change();
        });
    }

    /**
     * Kembalikan ke nullable jika rollback.
     */
    public function down(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->change();
        });
    }
};
