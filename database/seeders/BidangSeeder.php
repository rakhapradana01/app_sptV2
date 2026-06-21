<?php

namespace Database\Seeders;

use App\Models\Dinas;
use App\Models\Bidang;
use App\Models\SubBidang;
use App\Models\User;
use Illuminate\Database\Seeder;

class BidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create or Find Dinas BPKAD
        $dinas = Dinas::firstOrCreate(
            ['nama_dinas' => 'Badan Pengelolaan Keuangan dan Aset Daerah (BPKAD)']
        );

        // 2. Create Bidang
        $bidangs = [
            'PAD' => ['Sub Bidang PAD 1', 'Sub Bidang PAD 2'],
            'PAPKD' => ['Sub Bidang PAPKD 1', 'Sub Bidang PAPKD 2'],
            'BMD' => ['Sub Bidang BMD 1', 'Sub Bidang BMD 2'],
            'Sekretariat' => ['Sub Bagian Perencanaan', 'Sub Bagian Keuangan'],
        ];

        foreach ($bidangs as $namaBidang => $subBidangs) {
            $bidang = Bidang::firstOrCreate([
                'dinas_id' => $dinas->id,
                'nama_bidang' => $namaBidang,
            ]);

            foreach ($subBidangs as $namaSub) {
                SubBidang::firstOrCreate([
                    'bidang_id' => $bidang->id,
                    'nama_sub_bidang' => $namaSub,
                ]);
            }
        }

        // 3. Update existing seeded users for testing
        $padBidang = Bidang::where('nama_bidang', 'PAD')->first();
        $padSubBidang = SubBidang::where('nama_sub_bidang', 'Sub Bidang PAD 1')->first();

        // Kasubid 1 -> Bidang PAD, Sub Bidang PAD 1
        $kasubid = User::where('username', 'kasubid1')->first();
        if ($kasubid && $padBidang && $padSubBidang) {
            $kasubid->update([
                'dinas_id' => $dinas->id,
                'bidang_id' => $padBidang->id,
                'sub_bidang_id' => $padSubBidang->id,
            ]);
        }

        // Kabid -> Bidang PAD
        $kabid = User::where('username', 'kabid1')->first();
        if ($kabid && $padBidang) {
            $kabid->update([
                'dinas_id' => $dinas->id,
                'bidang_id' => $padBidang->id,
            ]);
        }

        // Kaban -> Dinas BPKAD (melihat semua)
        $kaban = User::where('username', 'kaban1')->first();
        if ($kaban) {
            $kaban->update([
                'dinas_id' => $dinas->id,
            ]);
        }
    }
}
