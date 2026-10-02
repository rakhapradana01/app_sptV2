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
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->text('disposisi_kabid')->nullable()->after('revisi');
            $table->timestamp('tanggal_disposisi_kabid')->nullable()->after('disposisi_kabid');

            $table->text('disposisi_sekban')->nullable()->after('tanggal_disposisi_kabid');
            $table->timestamp('tanggal_disposisi_sekban')->nullable()->after('disposisi_sekban');

            $table->text('disposisi_kaban')->nullable()->after('tanggal_disposisi_sekban');
            $table->timestamp('tanggal_disposisi_kaban')->nullable()->after('disposisi_kaban');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->dropColumn([
                'disposisi_kabid',
                'tanggal_disposisi_kabid',
                'disposisi_sekban',
                'tanggal_disposisi_sekban',
                'disposisi_kaban',
                'tanggal_disposisi_kaban',
            ]);
        });
    }
};
