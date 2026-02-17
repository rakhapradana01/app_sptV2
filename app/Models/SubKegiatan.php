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
        'pagu'
    ];
}
