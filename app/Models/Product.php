<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['vendor_id', 'kode', 'name', 'category', 'description', 'harga_supplier', 'harga_jual', 'unit', 'is_active'];

    protected $casts = ['is_active' => 'boolean', 'harga_jual' => 'decimal:2', 'harga_supplier' => 'decimal:2'];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
