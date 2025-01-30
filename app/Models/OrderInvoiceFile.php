<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderInvoiceFile extends Model
{
    protected $fillable = [
        'order_id',
        'file_path',
    ];

    /**
     * Sipariş ile birebir ilişki.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
