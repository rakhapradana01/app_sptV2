<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaDinas extends Model
{
    protected $table = 'nota_dinas';

    protected $fillable = [
        'sub_kegiatan_id',
        'tanggal',
        'kepada_id',
        'dari_id',
        'melalui_id',
        'perihal',
        'lokasi',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

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
    
}
