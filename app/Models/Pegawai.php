<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pegawai extends Model
{
    protected $fillable = [
        'nama',
        'nip',
        'pangkat',
        'jabatan',
        'dinas_id',
        'bidang_id',
    ];

    public function dinas(): BelongsTo
    {
        return $this->belongsTo(Dinas::class);
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class);
    }

    public function subKegiatans()
    {
        return $this->hasMany(SubKegiatan::class, 'pegawai_kasubid_id');
    }
    public function notaDinas()
    {
        return $this->belongsToMany(NotaDinas::class, 'nota_dinas_pegawai');
    }
}
