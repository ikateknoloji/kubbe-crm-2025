<?php

namespace App\Http\Controllers\V1\Shipping;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Rules\ShippingRule;

class ShippingController extends Controller
{
    /**
     * Belirtilen siparişleri SHP -> PD durumuna günceller.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateShippingStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_ids' => ['required', 'array', 'min:1', new ShippingRule()],
            'order_ids.*' => 'integer|exists:orders,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Geçersiz veri girişi.',
                'errors' => $validator->errors()
            ], 422);
        }

        $orderIds = $request->input('order_ids');
        $updatedCount = Order::whereIn('id', $orderIds)
                            ->update(['status' => 'PD']); 

        return response()->json([
            'message' => "Toplam {$updatedCount} sipariş başarıyla güncellendi ve ilgili aşamaya geçirildi.",
        ], 200);
    }
}
