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
            // Make nota_dinas_id nullable
            $table->foreignId('nota_dinas_id')->nullable()->change();

            // Add nullable spt_id foreign key
            $table->foreignId('spt_id')
                ->nullable()
                ->after('nota_dinas_id')
                ->constrained('spts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spj_rincians', function (Blueprint $table) {
            // Drop spt_id column
            $table->dropForeign(['spt_id']);
            $table->dropColumn('spt_id');

            // Make nota_dinas_id not null again
            $table->foreignId('nota_dinas_id')->nullable(false)->change();
        });
    }
};
