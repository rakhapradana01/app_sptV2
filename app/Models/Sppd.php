<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sppd extends Model
{
    protected $fillable = [
        'nota_dinas_id',
        'nomor_sppd',
        'alat_angkutan',
        'tempat_berangkat',
        'tempat_tujuan',
        'tanggal_sppd',
        'tempat_tujuan_2'
    ];
}
