<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sppd extends Model
{
    protected $fillable = [
        'nota_dinas_id',
        'nomor_sppd',
        'alat_angkutan',
        'tempat_berangkat',
        'tempat_tujuan',
        'tanggal_sppd',
        'tempat_tujuan_2',
        // Standalone fields
        'tanggal_mulai',
        'tanggal_selesai',
        'kegiatan',
        'nomor_spt_ref',
        'dinas_id',
        'bidang_id',
        'sub_bidang_id',
    ];

    protected $casts = [
        'tanggal_sppd'   => 'date',
        'tanggal_mulai'  => 'date',
        'tanggal_selesai' => 'date',
    ];

    // =====================
    // Relations
    // =====================

    public function notaDinas(): BelongsTo
    {
        return $this->belongsTo(NotaDinas::class, 'nota_dinas_id');
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

    /**
     * Pegawai yang terlampir langsung pada SPPD (untuk standalone).
     */
    public function pegawais()
    {
        return $this->belongsToMany(Pegawai::class, 'sppd_pegawai');
    }

    // =====================
    // Helpers
    // =====================

    /**
     * Apakah SPPD ini berdiri sendiri (tanpa Nota Dinas)?
     */
    public function isStandalone(): bool
    {
        return is_null($this->nota_dinas_id);
    }

    /**
     * Ambil tanggal mulai dari SPPD sendiri atau dari Nota Dinas.
     */
    public function getTanggalMulaiEfektifAttribute()
    {
        return $this->tanggal_mulai ?? $this->notaDinas?->tanggal_mulai;
    }

    /**
     * Ambil tanggal selesai dari SPPD sendiri atau dari Nota Dinas.
     */
    public function getTanggalSelesaiEfektifAttribute()
    {
        return $this->tanggal_selesai ?? $this->notaDinas?->tanggal_selesai;
    }

    /**
     * Ambil kegiatan dari SPPD sendiri atau dari Nota Dinas.
     */
    public function getKegiatanEfektifAttribute()
    {
        return $this->kegiatan ?? $this->notaDinas?->kegiatan;
    }

    /**
     * Hitung lama hari perjalanan.
     */
    public function getLamaHariAttribute(): int
    {
        $mulai  = $this->tanggal_mulai_efektif
            ? \Carbon\Carbon::parse($this->tanggal_mulai_efektif)->startOfDay()
            : null;
        $selesai = $this->tanggal_selesai_efektif
            ? \Carbon\Carbon::parse($this->tanggal_selesai_efektif)->startOfDay()
            : $mulai;

        if (!$mulai) return 1;

        return (int) $mulai->diffInDays($selesai) + 1;
    }

    /**
     * Ambil daftar pegawai dari SPPD sendiri atau dari Nota Dinas.
     */
    public function getPegawaisEfektifAttribute()
    {
        if ($this->isStandalone()) {
            return $this->pegawais;
        }
        return $this->notaDinas?->pegawais ?? collect();
    }
}
