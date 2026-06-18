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
        Schema::table('spts', function (Blueprint $table) {
            $table->year('tahun_anggaran')->nullable()->after('jenis_anggaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spts', function (Blueprint $table) {
            $table->dropColumn('tahun_anggaran');
        });
    }
};
