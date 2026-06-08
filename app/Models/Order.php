<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'ship_id', 'total_price', 'status', 'notes', 'pickup_date', 'pickup_time', 'pickup_location'];

    protected $casts = ['total_price' => 'decimal:2'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ship()
    {
        return $this->belongsTo(Ship::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function pos()
    {
        return $this->hasMany(OrderPo::class);
    }
}
