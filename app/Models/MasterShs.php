<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterShs extends Model
{
    protected $table = 'master_shs';

    protected $fillable = [
        'lokasi_tujuan',
        'tahun_anggaran',
        'uang_harian',
        'tiket_pesawat',
        'penginapan',
        'transport_bjb_rumah_bandara',
        'transport_bjb_bandara_rumah',
        'transport_tujuan_bandara_hotel',
        'transport_tujuan_hotel_bandara',
        'is_active',
    ];

    protected $casts = [
        'uang_harian'                    => 'decimal:2',
        'tiket_pesawat'                  => 'decimal:2',
        'penginapan'                     => 'decimal:2',
        'transport_bjb_rumah_bandara'    => 'decimal:2',
        'transport_bjb_bandara_rumah'    => 'decimal:2',
        'transport_tujuan_bandara_hotel' => 'decimal:2',
        'transport_tujuan_hotel_bandara' => 'decimal:2',
        'is_active'                      => 'boolean',
        'tahun_anggaran'                 => 'integer',
    ];

    /**
     * Total transport kota Banjarbaru (Rumah - Bandara PP)
     */
    public function getTotalTransportBjbAttribute(): float
    {
        return (float) ($this->transport_bjb_rumah_bandara + $this->transport_bjb_bandara_rumah);
    }

    /**
     * Total transport tujuan (Bandara - Hotel - Acara PP)
     */
    public function getTotalTransportTujuanAttribute(): float
    {
        return (float) ($this->transport_tujuan_bandara_hotel + $this->transport_tujuan_hotel_bandara);
    }

    /**
     * Total pagu transportasi keseluruhan
     */
    public function getTotalTransportAttribute(): float
    {
        return $this->total_transport_bjb + $this->total_transport_tujuan;
    }

    /**
     * Detail tarif penginapan per golongan/kelas jabatan
     */
    public function penginapan(): HasMany
    {
        return $this->hasMany(MasterShsPenginapan::class, 'master_shs_id');
    }

    /**
     * Detail tarif transportasi per rute
     */
    public function transportasi(): HasMany
    {
        return $this->hasMany(MasterShsTransportasi::class, 'master_shs_id');
    }
}
