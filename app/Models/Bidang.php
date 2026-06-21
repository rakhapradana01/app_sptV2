<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    protected $fillable = ['dinas_id', 'nama_bidang'];

    public function dinas()
    {
        return $this->belongsTo(Dinas::class);
    }

    public function subBidangs()
    {
        return $this->hasMany(SubBidang::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
