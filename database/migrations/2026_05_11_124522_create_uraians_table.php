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
      Schema::create('uraians', function (Blueprint $table) {
            $table->id();
            $table->integer('sub_kegiatan_id');
            $table->string('uraian');
            $table->integer('ok_total');

            $table->decimal('ok_terpakai', 15, 2);
            $table->decimal('harga_satuan', 15, 2);

            $table->bigInteger('total_anggaran');

            $table->decimal('anggaran_terpakai', 15, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uraians');
    }
};
