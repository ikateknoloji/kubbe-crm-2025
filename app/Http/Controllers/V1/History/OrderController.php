<?php

namespace App\Http\Controllers\V1\History;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\CustomerOrderCollection;
use App\Models\CustomerOrder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Ay ve yıl bilgisine göre müşterinin siparişlerini getir.
     *
     * @param Request $request
     * @return CustomerOrderCollection
     */
    public function index(Request $request)
    {
        $customerId = Auth::id();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $month = $request->input('month', $currentMonth);
        $year = $request->input('year', $currentYear);

        $customerOrders = CustomerOrder::with(['order'])
            ->where('customer_id', $customerId)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        return new CustomerOrderCollection($customerOrders);
    }

    /**
     * Belirli bir müşteri ID'sine göre siparişleri getir.
     */
    public function getOrdersByCustomerId($customerId, Request $request)
    {
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);

        $orders = CustomerOrder::with('order')
            ->where('customer_id', $customerId)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        return new CustomerOrderCollection($orders);
    }
}
