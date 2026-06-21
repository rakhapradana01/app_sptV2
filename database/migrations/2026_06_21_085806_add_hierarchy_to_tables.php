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
        $tables = ['users', 'nota_dinas', 'spts', 'sub_kegiatans'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table_bp) {
                $table_bp->foreignId('dinas_id')->nullable()->constrained('dinas')->nullOnDelete();
                $table_bp->foreignId('bidang_id')->nullable()->constrained('bidangs')->nullOnDelete();
                $table_bp->foreignId('sub_bidang_id')->nullable()->constrained('sub_bidangs')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['users', 'nota_dinas', 'spts', 'sub_kegiatans'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table_bp) {
                $table_bp->dropForeign(['dinas_id']);
                $table_bp->dropColumn('dinas_id');
                $table_bp->dropForeign(['bidang_id']);
                $table_bp->dropColumn('bidang_id');
                $table_bp->dropForeign(['sub_bidang_id']);
                $table_bp->dropColumn('sub_bidang_id');
            });
        }
    }
};
