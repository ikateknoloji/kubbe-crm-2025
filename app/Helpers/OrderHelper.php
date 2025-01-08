<?php

namespace App\Helpers;

use App\Models\Order;
use App\Models\CustomerOrder;
use App\Models\ManufacturerOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderHelper
{
    /**
    * Müşteri Siparişlerini Oluşturur.
    *
    * @param Order $order
    * @return void
    */
    public static function createCustomerOrder(Order $order)
    {
    
        $totalAmount = $order->orderItems()->sum('quantity');
        $averageUnitPrice = $order->orderItems()->avg('unit_price');
      
        CustomerOrder::create([
         'customer_id' => $order->customer_id,
         'order_id' => $order->id,
         'total_amount' => $totalAmount,
         'average_unit_price' => $averageUnitPrice,
         'total_price' => $order->offer_price,
        ]);
    }

    /**
     * Üretici Siparişini Oluşturur.
     *
     * @param Order $order
     * @return void
     */
    public static function createManufacturerOrder(Order $order)
    {
        // Aynı sipariş için birden fazla üretici siparişi oluşturulmasını önleyin
        $existing = ManufacturerOrder::where('order_id', $order->id)->first();

        if (!$existing) {
            ManufacturerOrder::create([
                'manufacturer_id' => $order->manufacturer_id,
                'order_id' => $order->id,
                'total_amount' => $order->orderItems()->sum('quantity'),
            ]);
        }
    }
}
