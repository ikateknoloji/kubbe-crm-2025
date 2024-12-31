<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLogo extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_basket_id',
        'image',
    ];

    /**
     * Get the order basket that owns the order logo.
     */
    public function orderBasket()
    {
        return $this->belongsTo(OrderBasket::class, 'order_basket_id');
    }
}
