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
        Schema::table('spj_rincians', function (Blueprint $table) {
            $table->foreignId('uraian_id')->nullable()->after('pegawai_id')->constrained('uraians')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spj_rincians', function (Blueprint $table) {
            $table->dropForeign(['uraian_id']);
            $table->dropColumn('uraian_id');
        });
    }
};
