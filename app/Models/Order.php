<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_name',
        'order_code',
        'status',
        'is_rejected',
        'note',
        'shipping_type',
        'invoice_status',
        'paid_amount',
        'offer_price',
        'customer_id',
        'manufacturer_id',
    ];

    /**
     * Get the customer associated with the order.
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the manufacturer associated with the order.
     */
    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

    /**
     * Get the order baskets associated with the order.
     */
    public function orderBaskets()
    {
        return $this->hasMany(OrderBasket::class, 'order_id');
    }


    public function paymentReceipts()
    {
        return $this->hasMany(OrderPaymentReceipt::class);
    }


    /**
     * İlişkiler: Fatura Bilgileri.
     *
     * Bir siparişin bir fatura bilgisi olabilir.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function invoiceInfo()
    {
        return $this->hasOne(InvoiceInfo::class);
    }

    public function shippingAddress()
    {
        return $this->hasOne(ShippingAddress::class, 'order_id');
    }

    public function orderImages()
    {
        return $this->hasMany(OrderImage::class, 'order_id');
    }
    
    public function scopeOrderByEnumStatus($query, $direction = 'asc')
    {
        return $query->orderByRaw("FIELD(status, '" . implode("','", OrderStatus::order()) . "') $direction");
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    /**
    * Get all the order logos associated with the order through order baskets.
    */
    public function orderLogos()
    {
        return $this->hasManyThrough(OrderLogo::class, OrderBasket::class, 'order_id', 'order_basket_id');
    }
}
