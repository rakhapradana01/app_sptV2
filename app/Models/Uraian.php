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
}
