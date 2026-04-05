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
        Schema::create('spts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nota_dinas_id')->constrained('nota_dinas')->onDelete('cascade');
            $table->string('nomor_spt');
            $table->enum('jenis_anggaran', ['DPA', 'DPPA'])->default('DPA');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spts');
    }
};
