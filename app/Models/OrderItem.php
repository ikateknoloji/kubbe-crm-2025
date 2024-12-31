<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_basket_id',
        'stock_id',
        'quantity',
        'unit_price',
    ];

    /**
     * Get the order basket that owns the order item.
     */
    public function orderBasket()
    {
        return $this->belongsTo(OrderBasket::class, 'order_basket_id');
    }

    /**
     * Get the stock associated with the order item.
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }
}
