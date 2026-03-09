<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RansumUpload extends Model
{
    protected $fillable = [
        'file_hash',
        'original_filename',
        'stored_filename',
        'vessel_code',
        'vessel_name',
        'voyage',
        'contact_person',
        'year',
        'date_start',
        'date_end',
        'jumlah_hari_pensupplaian',
        'eta',
        'vessel_route',
        'rute_sekarang',
        'port_tujuan',
        'currency',
        'conversi_rupiah',
        'jumlah_crew',
        'vendor_name',
        'barang_non_bkp',
        'barang_bkp',
        'pajak_11',
        'budget',
        'total_belanja_ransum',
        'selisih_anggaran',
        'status',
        'uploaded_by',
        'imported_at',
    ];

    protected $casts = [
        'imported_at' => 'datetime',
        'barang_non_bkp' => 'decimal:2',
        'barang_bkp' => 'decimal:2',
        'pajak_11' => 'decimal:2',
        'budget' => 'decimal:2',
        'total_belanja_ransum' => 'decimal:2',
        'selisih_anggaran' => 'decimal:2',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RansumItem::class);
    }
}
