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

    public function subKegiatan()
    {
        return $this->belongsTo(SubKegiatan::class, 'sub_kegiatan_id');
    }

    public function spjRincians()
    {
        return $this->hasMany(SpjRincian::class, 'uraian_id');
    }
}
