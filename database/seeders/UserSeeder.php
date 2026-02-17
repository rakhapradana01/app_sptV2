<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    public function run(): void
    {
        $role = Role::where('name', 'super_admin')->first();

        User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin00',
            'password' => Hash::make('Password123'),
            'role_id' => $role->id
        ]);
    }
}
