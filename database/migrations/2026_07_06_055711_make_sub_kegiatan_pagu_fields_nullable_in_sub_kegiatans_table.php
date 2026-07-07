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
        Schema::table('sub_kegiatans', function (Blueprint $table) {
            $table->integer('harga_satuan')->nullable()->change();
            $table->integer('koefisien')->nullable()->change();
            $table->bigInteger('pagu')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sub_kegiatans', function (Blueprint $table) {
            $table->integer('harga_satuan')->nullable(false)->change();
            $table->integer('koefisien')->nullable(false)->change();
            $table->bigInteger('pagu')->nullable(false)->change();
        });
    }
};
