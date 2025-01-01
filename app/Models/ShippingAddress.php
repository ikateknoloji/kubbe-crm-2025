<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingAddress extends Model
{
    use HasFactory;

    protected $table = 'shipping_addresses';

    protected $fillable = [
        'order_id',
        'full_name',
        'address',
        'city',
        'district',
        'country',
        'phone',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
