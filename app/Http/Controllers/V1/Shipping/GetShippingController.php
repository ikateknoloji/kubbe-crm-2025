<?php

namespace App\Http\Controllers\V1\Shipping;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoicedOrderResource;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ShippingResource;
use App\Http\Resources\SingleOrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class GetShippingController extends Controller
{
    /**
     * Kargo bilgisi eksik olan siparişleri getir.
     */
    public function getPendingShippingOrders()
    {
        $customerId = Auth::id(); 


        $orders = Order::with(['customer', 'manufacturer', 'orderLogos'])
        ->where('status', '=', 'p')
        ->where('customer_id', '=', $customerId) 
        ->paginate(9);
        
        return response()->json(new OrderCollection($orders));
    }

    /**
     * Kargo Bekleyen Siparişler
     */
    public function getShippedOrders()
    {
        $orders = Order::with(['shippingAddress', 'orderImages'])
                       ->where('status', '=', 'SHP')
                       ->whereNotNull('shipping_type') 
                       ->paginate(9); 
                       
        return response()->json(new OrderCollection($orders));
    }


}
