<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dinas extends Model
{
    protected $fillable = ['nama_dinas'];

    public function bidangs()
    {
        return $this->hasMany(Bidang::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
