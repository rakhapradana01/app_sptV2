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
        Schema::table('sppds', function (Blueprint $table) {
            $table->foreignId('dinas_id')->nullable()->constrained('dinas')->nullOnDelete();
            $table->foreignId('bidang_id')->nullable()->constrained('bidangs')->nullOnDelete();
            $table->foreignId('sub_bidang_id')->nullable()->constrained('sub_bidangs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppds', function (Blueprint $table) {
            $table->dropForeign(['dinas_id']);
            $table->dropColumn('dinas_id');
            $table->dropForeign(['bidang_id']);
            $table->dropColumn('bidang_id');
            $table->dropForeign(['sub_bidang_id']);
            $table->dropColumn('sub_bidang_id');
        });
    }
};
