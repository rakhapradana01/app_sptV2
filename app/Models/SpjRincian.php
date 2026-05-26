<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpjRincian extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_rekening',
        'nota_dinas_id',
        'pegawai_id',
        'uraian_id',
        'jumlah_hari',
        'uang_harian',
        'tiket_pesawat_pergi',
        'tiket_pesawat_pulang',
        'transport',
        'penginapan',
        'total',
    ];

    protected static function booted()
    {
        static::saved(function ($rincian) {
            $rincian->syncUraian();
        });

        static::deleted(function ($rincian) {
            $rincian->syncUraian();
        });
    }

    public function syncUraian()
    {
        $originalUraianId = $this->getOriginal('uraian_id');
        
        if ($this->uraian_id) {
            $this->recalculateUraian($this->uraian_id);
        }
        if ($originalUraianId && $originalUraianId != $this->uraian_id) {
            $this->recalculateUraian($originalUraianId);
        }
    }

    private function recalculateUraian($uraianId)
    {
        $uraian = \App\Models\Uraian::find($uraianId);
        if ($uraian) {
            $rincians = static::where('uraian_id', $uraianId)->get();
            $okTerpakai = count($rincians);
            $anggaranTerpakai = $rincians->sum('total');
            
            $uraian->update([
                'ok_terpakai' => $okTerpakai,
                'anggaran_terpakai' => $anggaranTerpakai
            ]);
        }
    }

    public function notaDinas(): BelongsTo
    {
        return $this->belongsTo(NotaDinas::class);
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function Uraian(): BelongsTo
    {
        return $this->belongsTo(Uraian::class);
    }
}
