<?php

namespace App\Http\Controllers\V1\Bill;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\SingleOrderResource;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class UninvoicedOrderController extends Controller
{
    /**
     * Fetch orders without invoice for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUninvoicedOrders(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', default: 10);
    
        $orders = Order::with(['customer', 'orderBaskets', 'orderBaskets.orderItem','orderBaskets.orderLogos'])
            ->where('customer_id', operator: Auth::id())
            ->orderBy('created_at', 'desc')
            ->whereDoesntHave(relation: 'invoiceInfo')
            ->where(column: 'invoice_status', operator: '=', value: 'P')
            ->paginate($perPage);

        return response()->json([
            'data' => OrderResource::collection($orders),
        ]);
    }

    /**
     * Fetch a single uninvoiced order for the authenticated user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSingleUninvoicedOrder(int $id): JsonResponse
    {
        $order = Order::with(['customer', 'orderBaskets.orderItem', 'orderBaskets.orderLogos'])
            ->where('customer_id', operator: Auth::id())
            ->findOrFail($id);
    
    
        return response()->json([
            'data' => new SingleOrderResource($order),
        ]);
    }
}
