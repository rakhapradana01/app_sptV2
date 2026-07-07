<?php

namespace Database\Seeders;

use App\Models\Bidang;
use App\Models\Dinas;
use App\Models\Role;
use App\Models\SubBidang;
use App\Models\User;
use App\Models\Pegawai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::where('name', 'super_admin')->first();
        $admin      = Role::where('name', 'admin')->first();
        $kasubid    = Role::where('name', 'kepala_sub_bidang')->first();
        $kabid      = Role::where('name', 'kepala_bidang')->first();
        $kaban      = Role::where('name', 'kepala_badan')->first();
        $userRole   = Role::where('name', 'user')->first();

        // Ambil hierarki yang sudah di-seed oleh BidangSeeder
        $dinas       = Dinas::where('nama_dinas', 'like', '%BPKAD%')->first();
        $bidangPAD   = Bidang::where('nama_bidang', 'PAD')->first();
        $subBidangP1 = SubBidang::where('nama_sub_bidang', 'Sub Bidang PAD 1')->first();
        $subBidangP2 = SubBidang::where('nama_sub_bidang', 'Sub Bidang PAD 2')->first();

        // Super Admin — akses global (dinas_id null)
        User::firstOrCreate(['username' => 'superadmin00'], [
            'name'     => 'Super Admin',
            'password' => Hash::make('Password123'),
            'role_id'  => $superAdmin->id,
        ]);

        // Admin Bidang — terikat Dinas + Bidang PAD
        User::firstOrCreate(['username' => 'admin01'], [
            'name'      => 'Admin Bidang',
            'password'  => Hash::make('Password123'),
            'role_id'   => $admin->id,
            'dinas_id'  => $dinas?->id,
            'bidang_id' => $bidangPAD?->id,
        ]);

        // Staff User — terikat Dinas + Bidang PAD
        User::firstOrCreate(['username' => 'user01'], [
            'name'      => 'Staff User',
            'password'  => Hash::make('Password123'),
            'role_id'   => $userRole->id,
            'dinas_id'  => $dinas?->id,
            'bidang_id' => $bidangPAD?->id,
        ]);

        // Cari pegawai untuk dihubungkan
        $pegawaiKasubid1 = Pegawai::where('nama', 'like', '%MUHAMMAD KHARIS ELYANI%')->first();
        $pegawaiKasubid2 = Pegawai::where('nama', 'like', '%ARIEF HIDAYAT%')->first();
        $pegawaiKabid    = Pegawai::where('nama', 'like', '%ADYA FERINA%')->first();
        $pegawaiKaban    = Pegawai::where('nama', 'like', '%FATKHAN%')->first();

        // Kasubid 1 — terikat Dinas + Bidang PAD + Sub Bidang PAD 1
        User::firstOrCreate(['username' => 'kasubid1'], [
            'name'          => 'Kasubid 1',
            'password'      => Hash::make('Password123'),
            'role_id'       => $kasubid->id,
            'pegawai_id'    => $pegawaiKasubid1?->id,
            'dinas_id'      => $dinas?->id,
            'bidang_id'     => $bidangPAD?->id,
            'sub_bidang_id' => $subBidangP1?->id,
        ]);

        // Kasubid 2 — terikat Dinas + Bidang PAD + Sub Bidang PAD 2
        User::firstOrCreate(['username' => 'kasubid2'], [
            'name'          => 'Kasubid 2',
            'password'      => Hash::make('Password123'),
            'role_id'       => $kasubid->id,
            'pegawai_id'    => $pegawaiKasubid2?->id,
            'dinas_id'      => $dinas?->id,
            'bidang_id'     => $bidangPAD?->id,
            'sub_bidang_id' => $subBidangP2?->id,
        ]);

        // Kabid — terikat Dinas + Bidang PAD
        User::firstOrCreate(['username' => 'kabid1'], [
            'name'      => 'Kabid PAD',
            'password'  => Hash::make('Password123'),
            'role_id'   => $kabid->id,
            'pegawai_id'=> $pegawaiKabid?->id,
            'dinas_id'  => $dinas?->id,
            'bidang_id' => $bidangPAD?->id,
        ]);

        // Kepala Badan — terikat Dinas saja
        User::firstOrCreate(['username' => 'kaban1'], [
            'name'     => 'Kepala Badan',
            'password' => Hash::make('Password123'),
            'role_id'  => $kaban->id,
            'pegawai_id'=> $pegawaiKaban?->id,
            'dinas_id' => $dinas?->id,
        ]);
    }
}
