<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderShipping extends Model
{
    protected $fillable = [
        'order_id',
        'tracking_code',
        'shipping_company',
    ];

    /**
     * Sipariş ile birebir ilişki.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
