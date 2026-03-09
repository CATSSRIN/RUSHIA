<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RansumItem extends Model
{
    protected $fillable = [
        'ransum_upload_id',
        'section',
        'nama_ransum',
        'kode_item',
        'items',
        'merk_spec',
        'ppn',
        'supplier',
        'harga',
        'satuan',
        'qty',
        'non_bkp',
        'bkp',
        'ppn_11',
        'ket_remarks',
        'status_received',
        'good_received',
    ];

    protected $casts = [
        'ppn' => 'decimal:2',
        'harga' => 'decimal:2',
        'qty' => 'decimal:4',
        'non_bkp' => 'decimal:2',
        'bkp' => 'decimal:2',
        'ppn_11' => 'decimal:2',
    ];

    public function ransumUpload(): BelongsTo
    {
        return $this->belongsTo(RansumUpload::class);
    }
}
