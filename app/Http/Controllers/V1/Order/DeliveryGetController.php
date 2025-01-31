<?php

namespace App\Http\Controllers\V1\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\SingleOrderResource;
use App\Models\Order;

class DeliveryGetController extends Controller
{
    /**
     * Get orders excluding 'OC' status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
    
        // Sadece 'P' durumundaki siparişleri getir
        $orders = Order::with(['customer', 'manufacturer', 'orderLogos'])
            ->where('status', 'P')
            ->orderByEnumStatus()
            ->paginate($perPage);
    
        return response()->json(new OrderCollection(resource: $orders->appends($request->query())));
    }
    
    
    /**
     * Get a single order by ID.
     */
    public function show(int $id)
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
            'orderItems',     
            'shipping'
        ])->findOrFail($id);

        return response()->json(new SingleOrderResource($order));
    }

    public function getTakeAwayOrders(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $orders = Order::with(['customer', 'manufacturer', 'orderLogos'])
            ->where('shipping_type', 'T')
            ->orderByEnumStatus()
            ->paginate($perPage);

        return response()->json(new OrderCollection(resource: $orders->appends($request->query())));
    }

    /**
     * Sadece 'SHP' durumunda olan ve belirli bir shipping_type değerine sahip siparişleri getirir.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getShippedOrdersByShippingType(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $shippingType = $request->get('shipping_type'); 
    
        if (!$shippingType) {
        return response()->json([
            'message' => 'shipping_type parametresi gereklidir.'
        ], 400);
        }
    
        $orders = Order::with(['customer', 'manufacturer', 'orderLogos'])
        ->where('status', 'SHP') 
        ->where('shipping_type', $shippingType)
        ->paginate($perPage);

        return response()->json(new OrderCollection(resource: $orders->appends($request->query())));
    }
    

}
