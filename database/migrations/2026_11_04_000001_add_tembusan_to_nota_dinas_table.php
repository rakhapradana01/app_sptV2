<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom tembusan pada nota dinas.
     *
     * Beberapa database lama sudah memiliki kolom ini secara manual, sehingga
     * penambahan dibuat idempoten agar aman dijalankan pada database tersebut.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('nota_dinas', 'tembusan')) {
            Schema::table('nota_dinas', function (Blueprint $table) {
                $table->text('tembusan')->nullable()->after('melalui_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('nota_dinas', 'tembusan')) {
            Schema::table('nota_dinas', function (Blueprint $table) {
                $table->dropColumn('tembusan');
            });
        }
    }
};
