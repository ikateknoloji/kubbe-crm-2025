<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderBasket extends Model
{
    use HasFactory;
    protected $fillable = ['order_id'];

    /**
     * Get the order that owns the basket.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Get the single order item associated with the basket.
     */
    public function orderItem()
    {
        return $this->hasOne(OrderItem::class, 'order_basket_id');
    }
    
    
    /**
     * Get the logos associated with the order basket.
     */
    public function orderLogos()
    {
        return $this->hasMany(OrderLogo::class, 'order_basket_id');
    }
    
}
