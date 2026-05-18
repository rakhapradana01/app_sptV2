<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    Schema::table('spj_rincians', function (Blueprint $table) {
        if (!Schema::hasColumn('spj_rincians', 'uraian_id')) {
            $table->foreignId('uraian_id')->nullable()->after('pegawai_id')->constrained('uraians')->onDelete('set null');
            echo "Successfully added uraian_id column!\n";
        } else {
            echo "Column uraian_id already exists!\n";
        }
    });
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
