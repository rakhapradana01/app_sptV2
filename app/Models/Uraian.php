<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Uraian extends Model
{
    protected $fillable = [
        'sub_kegiatan_id',
        'uraian',
        'ok_total',
        'ok_terpakai',
        'harga_satuan',
        'total_anggaran',
        'anggaran_terpakai',
    ];

    protected $casts = [
        'ok_total' => 'integer',
        'ok_terpakai' => 'integer',
        'harga_satuan' => 'integer',
        'total_anggaran' => 'integer',
        'anggaran_terpakai' => 'integer',
    ];

    protected static function booted()
    {
        static::saved(function ($uraian) {
            $uraian->updateSubKegiatanTotals();
        });

        static::deleted(function ($uraian) {
            $uraian->updateSubKegiatanTotals();
        });
    }

    public function updateSubKegiatanTotals()
    {
        $subKegiatan = $this->subKegiatan;
        if ($subKegiatan) {
            $subKegiatan->koefisien = $subKegiatan->uraians()->sum('ok_total');
            $subKegiatan->pagu = $subKegiatan->uraians()->sum('total_anggaran');
            $subKegiatan->save();
        }
    }

    public function subKegiatan()
    {
        return $this->belongsTo(SubKegiatan::class, 'sub_kegiatan_id');
    }

    public function spjRincians()
    {
        return $this->hasMany(SpjRincian::class, 'uraian_id');
    }
}
