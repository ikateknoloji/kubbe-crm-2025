<?php

namespace App\Models;

use App\Helpers\OrderHelper;
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
    
    protected $casts = [
        'status' => OrderStatus::class,
    ];

    protected static function boot()
    {
        parent::boot();
    
        static::deleting(function (Order $order) {
            $order->customerOrder()->delete();
            $order->manufacturerOrder()->delete();
        });
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }
    
    public function customerInfo()
    {
        return $this->hasOne(CustomerInfo::class, 'order_id');
    }

    public function orderBaskets()
    {
        return $this->hasMany(OrderBasket::class, 'order_id');
    }

    public function paymentReceipt()
    {
        return $this->hasOne(OrderPaymentReceipt::class);
    }

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

    public function orderLogos()
    {
        return $this->hasManyThrough(OrderLogo::class, OrderBasket::class, 'order_id', 'order_basket_id');
    }

    public function orderItems()
    {
        return $this->hasManyThrough(OrderItem::class, OrderBasket::class, 'order_id', 'order_basket_id', 'id', 'id');
    }

    public function timeline()
    {
        return $this->hasOne(OrderTimeline::class);
    }
    
    public function invoiceFile()
    {
        return $this->hasOne(OrderInvoiceFile::class);
    }

    public function shipping()
    {
        return $this->hasOne(OrderShipping::class);
    }

    public function manufacturerOrder()
    {
        return $this->hasOne(ManufacturerOrder::class, 'order_id');
    }

    public function customerOrder()
    {
        return $this->hasOne(CustomerOrder::class, 'order_id');
    }


}
