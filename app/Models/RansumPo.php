<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RansumPo extends Model
{
    protected $table = 'ransum_pos';

    protected $fillable = [
        'ransum_upload_id',
        'supplier_key',
        'vendor_name',
        'po_number',
        'pdf_path',
        'status',
    ];

    public function ransumUpload(): BelongsTo
    {
        return $this->belongsTo(RansumUpload::class);
    }
}
