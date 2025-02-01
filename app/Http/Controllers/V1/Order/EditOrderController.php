<?php

namespace App\Http\Controllers\V1\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\EditOrderResource;
use Illuminate\Http\Request;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\SingleOrderResource;
use Illuminate\Http\JsonResponse;
use App\Models\Order;

class EditOrderController extends Controller
{
    /**
     * Fetch a single order with its related baskets and their relationships.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $order = Order::with([
            'customer',
            'manufacturer',
            'orderBaskets',
            'orderBaskets.orderItem.stock.productType',
            'orderBaskets.orderItem.stock.color',
            'orderBaskets.orderLogos',
            'invoiceInfo',
            'paymentReceipt',
            'shippingAddress',    
            'orderImages',  
            'customerInfo',
            'timeline',     
            'shipping'
        ])->findOrFail($id);
        

        return response()->json(new EditOrderResource($order));
    }
}
