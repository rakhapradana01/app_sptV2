<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Spt extends Model
{
    use HasFactory;

    // Tentukan kolom yang bisa diisi mass-assignment
    protected $fillable = [
        'nota_dinas_id',
        'nomor_spt',
        'jenis_anggaran'
    ];

    // Jika kamu ingin otomatis mengubah string tanggal menjadi objek Carbon (agar mudah diformat)


    public function notaDinas(): BelongsTo
    {
        return $this->belongsTo(NotaDinas::class, 'nota_dinas_id');
    }

    public function getDurasiHariAttribute()
    {
        $mulai = \Carbon\Carbon::parse($this->notaDinas->tanggal_mulai);
        $selesai = \Carbon\Carbon::parse($this->notaDinas->tanggal_selesai);

        return $mulai->diffInDays($selesai) + 1;
    }
}
