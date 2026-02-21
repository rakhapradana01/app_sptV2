<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class UserSeeder extends Seeder
{

    public function run(): void
    {
        $superAdmin = Role::where('name', 'super_admin')->first();
        $kasubid    = Role::where('name', 'kepala_sub_bidang')->first();
        $kabid      = Role::where('name', 'kepala_bidang')->first();
        $kaban      = Role::where('name', 'kepala_badan')->first();
        $userRole   = Role::where('name', 'user')->first();

        User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin00',
            'password' => Hash::make('Password123'),
            'role_id' => $superAdmin->id
        ]);

        User::create([
            'name' => 'Staff User',
            'username' => 'user01',
            'password' => Hash::make('Password123'),
            'role_id' => $userRole->id
        ]);

        User::create([
            'name' => 'Kasubid 1',
            'username' => 'kasubid1',
            'password' => Hash::make('Password123'),
            'role_id' => $kasubid->id
        ]);

        User::create([
            'name' => 'Kabid',
            'username' => 'kabid1',
            'password' => Hash::make('Password123'),
            'role_id' => $kabid->id
        ]);

        User::create([
            'name' => 'Kepala Badan',
            'username' => 'kaban1',
            'password' => Hash::make('Password123'),
            'role_id' => $kaban->id
        ]);
        // dd(Auth::user()->role->name);
    }
}
