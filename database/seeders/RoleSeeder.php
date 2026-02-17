<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'super_admin',
            'user',
            'admin',
            'kepala_bidang',
            'kepala_sub_bidang',
            'kepala_badan',
        ];

        foreach ($roles as  $value) {
            Role::create(['name' => $value]);
        }
    }
}
