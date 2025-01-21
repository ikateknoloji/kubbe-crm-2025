<?php

namespace App\Http\Controllers\V1\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\SingleOrderResource;
use App\Models\Order;

class DesingerGetController extends Controller
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
        $status = $request->get('status'); // Optional filtering by other statuses

        // Temel sorgu, 'OC' durumundaki siparişleri hariç tutar
        $query = Order::with(['customer', 'manufacturer', 'orderLogos'])
            ->where('status', '!=', 'OC');

        // Eğer başka bir durum belirtildiyse bunu uygula
        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->orderByEnumStatus()->paginate($perPage);

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
            'customerInfo'
        ])->findOrFail($id);

        return response()->json(new SingleOrderResource($order));
    }

}
