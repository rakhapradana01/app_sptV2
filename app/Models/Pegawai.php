<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $fillable = [
        'nama',
        'nip',
        'pangkat',
        'jabatan'
    ];

    public function subKegiatans()
    {
        return $this->hasMany(SubKegiatan::class, 'pegawai_kasubid_id');
    }
    // public function notaDinas()
    // {
    //     return $this->hasMany(NotaDinas::class, 'pegawai_kasubid_id');
    // }
}
