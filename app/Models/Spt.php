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
        $selesai = $this->notaDinas->tanggal_selesai 
            ? \Carbon\Carbon::parse($this->notaDinas->tanggal_selesai) 
            : $mulai;

        return $mulai->diffInDays($selesai) + 1;
    }

    public function getHasRealNomorAttribute(): bool
    {
        if (empty($this->nomor_spt)) {
            return false;
        }
        // If it contains 3 or more spaces in a row (e.g. placeholder template), it's not a real number
        if (preg_match('/\s{3,}/', $this->nomor_spt)) {
            return false;
        }
        return true;
    }
}
