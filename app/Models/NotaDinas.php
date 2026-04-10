<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaDinas extends Model
{
    protected $table = 'nota_dinas';

    protected $fillable = [
        'nomor_urut',
        'sub_kegiatan_id',
        'tanggal',
        'kepada_id',
        'dari_id',
        'melalui_id',
        'asal_undangan',
        'perihal',
        'lokasi',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'kegiatan'
    ];

    const DRAFT = 'draft';
    const DIAJUKAN_KASUBID = 'diajukan_kasubid';
    const DISETUJUI_KASUBID = 'disetujui_kasubid';
    const DIAJUKAN_KABID = 'diajukan_kabid';
    const DISETUJUI_KABID = 'disetujui_kabid';
    const DIAJUKAN_KABAN = 'diajukan_kaban';
    const DISETUJUI_KABAN = 'disetujui_kaban';

    public function subKegiatan()
    {
        return $this->belongsTo(SubKegiatan::class);
    }

    public function kepada()
    {
        return $this->belongsTo(Pegawai::class, 'kepada_id');
    }

    public function melalui()
    {
        return $this->belongsTo(Pegawai::class, 'melalui_id');
    }

    public function dari()
    {
        return $this->belongsTo(Pegawai::class, 'dari_id');
    }

    public function pegawais()
    {
        return $this->belongsToMany(
            Pegawai::class,
            'nota_dinas_pegawai'
        );
    }

    public function spt()
    {
        return $this->hasOne(Spt::class, 'nota_dinas_id');
    }
    public function sppd()
    {
        return $this->hasOne(Sppd::class, 'nota_dinas_id');
    }
}
