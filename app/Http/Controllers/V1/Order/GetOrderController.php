<?php

namespace App\Http\Controllers\V1\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\SingleOrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GetOrderController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 1);
        $status = $request->get('status'); 

        $query = Order::with(['customer', 'manufacturer', 'orderLogos']);
    
        if ($status) {
            $query->where('status', $status); 
        }
    
        $orders = $query->orderByEnumStatus()->paginate($perPage);
    
        return response()->json(new OrderCollection(resource: $orders->appends($request->query())));
    }

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
        

        return response()->json(new SingleOrderResource($order));
    }

}
