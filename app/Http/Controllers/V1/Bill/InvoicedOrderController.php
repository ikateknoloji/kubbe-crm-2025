<?php

namespace App\Http\Controllers\V1\Bill;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Bill\InvoicedOrderResource;
use App\Http\Resources\OrderCollection;

class InvoicedOrderController extends Controller
{
    public function getInvoicedOrders(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
    
        $orders = Order::with([
                'customer', 
                'orderBaskets.orderItem.stock.productType',
                'orderBaskets.orderItem.stock.color',
                'orderBaskets.orderLogos', 
                'invoiceInfo'
            ])
            ->where('invoice_status', 'P')
            ->whereHas('invoiceInfo')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

            $data = new OrderCollection($orders->appends($request->query()));

        return response()->json($data);
    }

    
    public function getSingleInvoicedOrder(int $id): JsonResponse
    {
        $order = Order::with([
            'customer', 
            'orderBaskets.orderItem.stock.productType',
            'orderBaskets.orderItem.stock.color',
            'orderBaskets.orderLogos', 
            'invoiceInfo'
        ])
        ->where('invoice_status', operator: 'P')
        ->find($id);
    
        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }
    
        return response()->json([
            'order' => new InvoicedOrderResource($order),
        ]);
    }
    
}
