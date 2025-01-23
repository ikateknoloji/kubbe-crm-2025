<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufacturerOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'total_amount', 
        'manufacturer_id'
    ];

    /**
     * İlişki: Bir üretici siparişi bir siparişe aittir.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * İlişki: Bir üretici siparişi bir üreticiye aittir.
     */
    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }
}
