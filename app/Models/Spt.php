<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Spt extends Model
{
    use HasFactory;

    protected $fillable = [
        'nota_dinas_id',
        'nomor_spt',
        'jenis_anggaran',
        'tahun_anggaran',
        // Standalone fields
        'tanggal_mulai',
        'tanggal_selesai',
        'lokasi',
        'kegiatan',
        'sub_kegiatan_id',
    ];

    protected $casts = [
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

    public function subKegiatan(): BelongsTo
    {
        return $this->belongsTo(SubKegiatan::class, 'sub_kegiatan_id');
    }

    /**
     * Pegawai yang terlampir langsung pada SPT (untuk standalone).
     */
    public function pegawais()
    {
        return $this->belongsToMany(Pegawai::class, 'spt_pegawai');
    }

    public function spjRincians()
    {
        return $this->hasMany(SpjRincian::class, 'spt_id');
    }

    // =====================
    // Helpers
    // =====================

    /**
     * Apakah SPT ini berdiri sendiri (tanpa Nota Dinas)?
     */
    public function isStandalone(): bool
    {
        return is_null($this->nota_dinas_id);
    }

    /**
     * Ambil tanggal mulai dari SPT sendiri atau dari Nota Dinas.
     */
    public function getTanggalMulaiEfektifAttribute()
    {
        return $this->tanggal_mulai ?? $this->notaDinas?->tanggal_mulai;
    }

    /**
     * Ambil tanggal selesai dari SPT sendiri atau dari Nota Dinas.
     */
    public function getTanggalSelesaiEfektifAttribute()
    {
        return $this->tanggal_selesai ?? $this->notaDinas?->tanggal_selesai;
    }

    /**
     * Ambil lokasi dari SPT sendiri atau dari Nota Dinas.
     */
    public function getLokasiEfektifAttribute()
    {
        return $this->lokasi ?? $this->notaDinas?->lokasi;
    }

    /**
     * Ambil kegiatan dari SPT sendiri atau dari Nota Dinas.
     */
    public function getKegiatanEfektifAttribute()
    {
        return $this->kegiatan ?? $this->notaDinas?->kegiatan;
    }

    /**
     * Ambil daftar pegawai dari SPT sendiri atau dari Nota Dinas.
     */
    public function getPegawaisEfektifAttribute()
    {
        if ($this->isStandalone()) {
            return $this->pegawais;
        }
        return $this->notaDinas?->pegawais ?? collect();
    }

    public function getDurasiHariAttribute()
    {
        $mulai = $this->tanggal_mulai_efektif
            ? \Carbon\Carbon::parse($this->tanggal_mulai_efektif)
            : null;

        $selesai = $this->tanggal_selesai_efektif
            ? \Carbon\Carbon::parse($this->tanggal_selesai_efektif)
            : $mulai;

        if (!$mulai) return 1;

        return $mulai->diffInDays($selesai) + 1;
    }

    public function getHasRealNomorAttribute(): bool
    {
        if (empty($this->nomor_spt)) {
            return false;
        }
        // If it contains 3 or more spaces in a row (e.g. placeholder template), it's not a real number
        if (preg_match('/\s{3,}/', $this->nomor_spt)) {
            return false;
        }
        return true;
    }

    public function getSpjRinciansEfektifAttribute()
    {
        if ($this->isStandalone()) {
            return $this->spjRincians;
        }
        return $this->notaDinas?->spjRincians ?? collect();
    }

    public function getSppdEfektifAttribute()
    {
        if ($this->isStandalone()) {
            return Sppd::where('nomor_spt_ref', $this->nomor_spt)->first();
        }
        return $this->notaDinas?->sppd;
    }
}
