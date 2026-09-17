<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'password',
        'role_id',
        'pegawai_id',
        'dinas_id',
        'bidang_id',
        'sub_bidang_id'
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }


    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function dinas(): BelongsTo
    {
        return $this->belongsTo(Dinas::class);
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class);
    }

    public function subBidang(): BelongsTo
    {
        return $this->belongsTo(SubBidang::class);
    }


    public function subKegiatans()
    {
        return $this->hasMany(SubKegiatan::class, 'user_id');
    }

    public function getAllSubKegiatansAttribute()
    {
        return SubKegiatan::where('user_id', $this->id)
            ->when($this->sub_bidang_id, fn($q) => $q->orWhere('sub_bidang_id', $this->sub_bidang_id))
            ->when($this->pegawai_id, fn($q) => $q->orWhere('pegawai_kasubid_id', $this->pegawai_id))
            ->get();
    }
    
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
    
}
