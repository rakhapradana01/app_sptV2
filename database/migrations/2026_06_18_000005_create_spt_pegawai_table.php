<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spt_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spt_id')->constrained('spts')->onDelete('cascade');
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');
            $table->unique(['spt_id', 'pegawai_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spt_pegawai');
    }
};
