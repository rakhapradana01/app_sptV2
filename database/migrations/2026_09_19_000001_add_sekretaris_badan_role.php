<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Role::firstOrCreate(['name' => 'sekretaris_badan']);
    }

    public function down(): void
    {
        Role::where('name', 'sekretaris_badan')->delete();
    }
};
