<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
