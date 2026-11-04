<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterShsPenginapan extends Model
{
    protected $table = 'master_shs_penginapan';

    protected $fillable = [
        'master_shs_id',
        'golongan_kelas',
        'tarif',
        'keterangan',
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
    ];

    public function masterShs(): BelongsTo
    {
        return $this->belongsTo(MasterShs::class, 'master_shs_id');
    }
}
