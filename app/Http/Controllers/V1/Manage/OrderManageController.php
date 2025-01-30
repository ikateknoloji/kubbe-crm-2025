<?php

namespace App\Http\Controllers\V1\Manage;

use App\Enums\OrderStatus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Manage\ApproveOrderRequest;
use App\Http\Requests\Manage\PrepareForShippingRequest;
use App\Http\Requests\Manage\AssignManufacturerRequest;
use App\Models\Order;
use App\Models\Manufacturer;
use Illuminate\Http\JsonResponse;

class OrderManageController extends Controller
{
    /**
     * Siparişi onaylayarak PRP aşamasına gönderir.
     *
     * @param \App\Http\Requests\Manage\ApproveOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveOrder(ApproveOrderRequest $request): JsonResponse
    {
        $orderId = $request->input('order_id');
        $paidAmount = $request->input('paid_amount');

        $order = Order::findOrFail($orderId);
        $order->update([
            'status'       => 'PRP',
            'paid_amount'  => $paidAmount
        ]);

        return response()->json([
            'message' => "Sipariş '{$order->order_name}' başarıyla onaylandı, ödeme miktarı: ₺{$paidAmount}"
        ], 200);
    }

    /**
     * Siparişi PRP aşamasından RFP aşamasına taşır.
     *
     * @param \App\Http\Requests\Manage\PrepareForShippingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function prepareForShipping(PrepareForShippingRequest $request): JsonResponse
    {
        $orderId = $request->input('order_id');
        $order = Order::findOrFail($orderId);
        $order->update(['status' => 'RFP']);

        return response()->json([
            'message' => "Sipariş '{$order->order_name}' başarıyla 'RFP' aşamasına alındı."
        ], 200);
    }

    /**
     * Bir siparişe üretici ata ve siparişi 'P' durumuna getir.
     *
     * @param \App\Http\Requests\Manage\AssignManufacturerRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignManufacturer(AssignManufacturerRequest $request): JsonResponse
    {
        $orderId = $request->input('order_id');
        $manufacturerId = $request->input('manufacturer_id');
        $order = Order::findOrFail($orderId);

        
        $order->update([
            'manufacturer_id' => $manufacturerId,
            'status'          => OrderStatus::P
        ]);

        $manufacturer = Manufacturer::findOrFail($manufacturerId);

        return response()->json([
            'message' => "Sipariş '{$order->order_name}' başarıyla '{$manufacturer->name}' üreticisine atandı ve 'P' aşamasına getirildi."
        ], 200);
    }
}
