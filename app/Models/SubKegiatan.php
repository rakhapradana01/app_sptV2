<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKegiatan extends Model
{
    protected $fillable = [
        'nomor_rekening',
        'nama_kegiatan',
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
