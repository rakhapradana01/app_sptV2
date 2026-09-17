<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SubKegiatan extends Model
{
    protected $fillable = [
        'nomor_rekening',
        'nama_kegiatan',
        'user_id',
        'pegawai_kasubid_id',
        'koefisien',
        'harga_satuan',
        'pagu',
        'dinas_id',
        'bidang_id',
        'sub_bidang_id'
    ];
    // public function realisasis()
    // {
    //     return $this->hasMany(Realisasi::class);
    // }

    public function getRealisasiAttribute()
    {
        return $this->uraians()->sum('anggaran_terpakai');
    }

    public function getSisaAttribute()
    {
        return $this->pagu - $this->realisasi;
    }

    /**
     * Mengambil nama PPTK yang sesuai dengan Sub Bidang / Kasubid
     */
    public function getPptkNamaAttribute()
    {
        // 1. Jika ada sub_bidang_id, cari user kasubid dari sub bidang tersebut
        if ($this->sub_bidang_id) {
            $kasubid = User::where('sub_bidang_id', $this->sub_bidang_id)
                ->whereHas('role', fn($q) => $q->where('name', 'kepala_sub_bidang'))
                ->with('pegawai')
                ->first();

            if ($kasubid) {
                return $kasubid->pegawai?->nama ?? $kasubid->name;
            }
        }

        // 2. Jika ada pegawai kasubid langsung yang bukan pegawai admin/super_admin
        if ($this->pegawai && (!$this->owner || !in_array($this->owner->role?->name, ['admin', 'super_admin']))) {
            return $this->pegawai->nama;
        }

        // 3. Jika owner adalah kasubid
        if ($this->owner && $this->owner->role?->name === 'kepala_sub_bidang') {
            return $this->owner->pegawai?->nama ?? $this->owner->name;
        }

        // 4. Jika ada pegawai kasubid
        if ($this->pegawai) {
            return $this->pegawai->nama;
        }

        // 5. Jika owner bukan admin / super admin
        if ($this->owner && !in_array($this->owner->role?->name, ['admin', 'super_admin'])) {
            return $this->owner->pegawai?->nama ?? $this->owner->name;
        }

        return '-';
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_kasubid_id');
    }

    public function dinas()
    {
        return $this->belongsTo(Dinas::class);
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    public function subBidang()
    {
        return $this->belongsTo(SubBidang::class);
    }

    public function uraians()
    {
        return $this->hasMany(Uraian::class, 'sub_kegiatan_id');
    }
}
