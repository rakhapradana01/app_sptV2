<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Spt extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 'draft';
    const STATUS_DIAJUKAN_KABID = 'diajukan_kabid';
    const STATUS_DIAJUKAN_SEKBAN = 'diajukan_sekban';
    const STATUS_DIAJUKAN_KABAN = 'diajukan_kaban';
    const STATUS_DISETUJUI_KABAN = 'disetujui_kaban';

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
        'dinas_id',
        'bidang_id',
        'sub_bidang_id',
        'status',
        'kasubid_id',
        'kasubid_approved_at',
        'kasubid_paraf',
        'kabid_id',
        'kabid_approved_at',
        'kabid_paraf',
        'sekban_id',
        'sekban_approved_at',
        'sekban_paraf',
        'kaban_id',
        'kaban_approved_at',
        'kaban_paraf',
    ];

    protected $casts = [
        'tanggal_mulai'        => 'date',
        'tanggal_selesai'      => 'date',
        'kasubid_approved_at'  => 'datetime',
        'kabid_approved_at'    => 'datetime',
        'sekban_approved_at'   => 'datetime',
        'kaban_approved_at'    => 'datetime',
    ];

    // =====================
    // Approval Helpers
    // =====================

    public function isApprovedByKasubid(): bool
    {
        return !is_null($this->kasubid_approved_at);
    }

    public function isApprovedByKabid(): bool
    {
        return !is_null($this->kabid_approved_at);
    }

    public function isApprovedBySekban(): bool
    {
        return !is_null($this->sekban_approved_at);
    }

    public function isApprovedByKaban(): bool
    {
        return !is_null($this->kaban_approved_at);
    }

    public function kasubid(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasubid_id');
    }

    public function kabid(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kabid_id');
    }

    public function sekban(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sekban_id');
    }

    public function kaban(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kaban_id');
    }

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
     * Pegawai yang terlampir langsung pada SPT (untuk standalone).
     */
    public function pegawais()
    {
        return $this->belongsToMany(Pegawai::class, 'spt_pegawai');
    }

    /**
     * SPPD yang merujuk ke SPT ini (untuk standalone).
     */
    public function sppds()
    {
        return $this->hasMany(Sppd::class, 'spt_id');
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
