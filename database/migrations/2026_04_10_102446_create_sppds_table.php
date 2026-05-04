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
        Schema::create('sppds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nota_dinas_id')->constrained('nota_dinas')->onDelete('cascade');
            $table->string('nomor_sppd');
            $table->string('alat_angkutan');
            $table->string('tempat_berangkat');
            $table->string('tempat_tujuan');
            $table->date('tanggal_sppd');
            $table->string('tempat_tujuan_2')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sppds');
    }
};
