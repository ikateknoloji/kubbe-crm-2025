<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTimeline extends Model
{
    protected $fillable = [
        'order_id',
        'approved_at',
        'production_started_at',
        'production_completed_at', 
        'shipped_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'production_started_at' => 'datetime',
        'production_completed_at' => 'datetime', 
        'shipped_at' => 'datetime',
    ];

    /**
     * Sipariş ile birebir ilişki.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
