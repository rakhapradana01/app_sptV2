<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubBidang extends Model
{
    protected $fillable = ['bidang_id', 'nama_sub_bidang'];

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
