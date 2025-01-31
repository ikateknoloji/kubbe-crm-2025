<?php

namespace App\Http\Controllers\V1\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Http\Resources\OrderCollection;
use App\Http\Resources\SingleOrderResource;
use App\Models\Order;

class CustomerGetController extends Controller
{
    /**
     * Get orders for the authenticated customer.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user(); // Auth ile oturum açmış kullanıcıyı alır
        $perPage = $request->get('per_page', 1);
        $status = $request->get('status'); 

        $query = Order::with(['customer', 'manufacturer', 'orderLogos'])
            ->where('customer_id', $user->id); // Sadece oturum açmış kullanıcının siparişleri

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->orderByEnumStatus()->paginate($perPage);

        return response()->json(new OrderCollection(resource: $orders->appends($request->query())));
    }

    /**
     * Fetch a single order for the authenticated customer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = Auth::user(); // Auth ile oturum açmış kullanıcıyı alır

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
        ])
        ->where('customer_id', $user->id) 
        ->findOrFail($id);

        return response()->json(new SingleOrderResource($order));
    }
}
