<?php

namespace App\Http\Controllers\V1\History;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManufacturerOrder;
use App\Http\Resources\ManufacturerOrderCollection;
class ManufacturerOrderController extends Controller
{
    /**
     * Belirli bir üretici ID'sine göre siparişleri getir.
     */
    public function getOrdersByManufacturerId($manufacturerId, Request $request)
    {
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);

        $orders = ManufacturerOrder::with('order')
            ->where('manufacturer_id', $manufacturerId)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        return new ManufacturerOrderCollection($orders);
    }
}
