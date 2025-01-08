<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'average_unit_price',
        'total_price',
        'total_amount',
    ];

    /**
     * İlişki: Bir müşteri siparişi bir siparişe aittir.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
