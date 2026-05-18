<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpjRincian extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_rekening',
        'nota_dinas_id',
        'pegawai_id',
        'jumlah_hari',
        'uang_harian',
        'tiket_pesawat_pergi',
        'tiket_pesawat_pulang',
        'transport',
        'total',
    ];

    public function notaDinas(): BelongsTo
    {
        return $this->belongsTo(NotaDinas::class);
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
