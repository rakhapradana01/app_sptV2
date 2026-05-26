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
        Schema::table('spj_rincians', function (Blueprint $table) {
            if (!Schema::hasColumn('spj_rincians', 'penginapan')) {
                $table->integer('penginapan')->default(0)->after('transport');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spj_rincians', function (Blueprint $table) {
            if (Schema::hasColumn('spj_rincians', 'penginapan')) {
                $table->dropColumn('penginapan');
            }
        });
    }
};
