<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_shs', function (Blueprint $table) {
            $table->decimal('penginapan', 15, 2)->default(0)->after('tiket_pesawat')->comment('Pagu maksimal penginapan');
            
            // Transport Kota Banjarbaru
            $table->decimal('transport_bjb_rumah_bandara', 15, 2)->default(0)->after('penginapan')->comment('Kota Banjarbaru: Rumah - Bandara');
            $table->decimal('transport_bjb_bandara_rumah', 15, 2)->default(0)->after('transport_bjb_rumah_bandara')->comment('Kota Banjarbaru: Bandara - Rumah');
            
            // Transport Kota Tujuan
            $table->decimal('transport_tujuan_bandara_hotel', 15, 2)->default(0)->after('transport_bjb_bandara_rumah')->comment('Tujuan: Bandara - Hotel - Acara');
            $table->decimal('transport_tujuan_hotel_bandara', 15, 2)->default(0)->after('transport_tujuan_bandara_hotel')->comment('Tujuan: Hotel - Bandara');
        });
    }

    public function down(): void
    {
        Schema::table('master_shs', function (Blueprint $table) {
            $table->dropColumn([
                'penginapan',
                'transport_bjb_rumah_bandara',
                'transport_bjb_bandara_rumah',
                'transport_tujuan_bandara_hotel',
                'transport_tujuan_hotel_bandara',
            ]);
        });
    }
};
