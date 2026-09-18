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
            $table->unsignedBigInteger('spt_id')->nullable()->after('nota_dinas_id');
            $table->foreign('spt_id')->references('id')->on('spts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppds', function (Blueprint $table) {
            $table->dropForeign(['spt_id']);
            $table->dropColumn('spt_id');
        });
    }
};
