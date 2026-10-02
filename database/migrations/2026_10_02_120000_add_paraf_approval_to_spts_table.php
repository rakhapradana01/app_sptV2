<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spts', function (Blueprint $table) {
            $table->string('status', 50)->default('draft')->after('sub_bidang_id');

            $table->foreignId('kasubid_id')->nullable()->constrained('users')->nullOnDelete()->after('status');
            $table->timestamp('kasubid_approved_at')->nullable()->after('kasubid_id');
            $table->string('kasubid_paraf')->nullable()->after('kasubid_approved_at');

            $table->foreignId('kabid_id')->nullable()->constrained('users')->nullOnDelete()->after('kasubid_paraf');
            $table->timestamp('kabid_approved_at')->nullable()->after('kabid_id');
            $table->string('kabid_paraf')->nullable()->after('kabid_approved_at');

            $table->foreignId('sekban_id')->nullable()->constrained('users')->nullOnDelete()->after('kabid_paraf');
            $table->timestamp('sekban_approved_at')->nullable()->after('sekban_id');
            $table->string('sekban_paraf')->nullable()->after('sekban_approved_at');

            $table->foreignId('kaban_id')->nullable()->constrained('users')->nullOnDelete()->after('sekban_paraf');
            $table->timestamp('kaban_approved_at')->nullable()->after('kaban_id');
            $table->string('kaban_paraf')->nullable()->after('kaban_approved_at');
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'signature_path')) {
                $table->string('signature_path')->nullable()->after('sub_bidang_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('spts', function (Blueprint $table) {
            $table->dropForeign(['kasubid_id']);
            $table->dropForeign(['kabid_id']);
            $table->dropForeign(['sekban_id']);
            $table->dropForeign(['kaban_id']);
            $table->dropColumn([
                'status',
                'kasubid_id', 'kasubid_approved_at', 'kasubid_paraf',
                'kabid_id', 'kabid_approved_at', 'kabid_paraf',
                'sekban_id', 'sekban_approved_at', 'sekban_paraf',
                'kaban_id', 'kaban_approved_at', 'kaban_paraf',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'signature_path')) {
                $table->dropColumn('signature_path');
            }
        });
    }
};
