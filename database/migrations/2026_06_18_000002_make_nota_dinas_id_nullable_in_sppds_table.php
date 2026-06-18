<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sppds', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['nota_dinas_id']);
            // Change to nullable
            $table->unsignedBigInteger('nota_dinas_id')->nullable()->change();
            // Re-add foreign key as nullable
            $table->foreign('nota_dinas_id')
                ->references('id')
                ->on('nota_dinas')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('sppds', function (Blueprint $table) {
            $table->dropForeign(['nota_dinas_id']);
            $table->unsignedBigInteger('nota_dinas_id')->nullable(false)->change();
            $table->foreign('nota_dinas_id')
                ->references('id')
                ->on('nota_dinas')
                ->onDelete('cascade');
        });
    }
};
