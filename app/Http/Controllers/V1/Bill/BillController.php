<?php

namespace App\Http\Controllers\V1\Bill;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bill\StoreInvoiceRequest;
use App\Http\Requests\Bill\UpdateInvoiceRequest;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class BillController extends Controller
{
    /**
     * Belirtilen sipariş için fatura bilgilerini kaydet.
     *
     * @param StoreInvoiceRequest $request
     * @param int $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreInvoiceRequest $request, $orderId): JsonResponse
    {
        $order = Order::findOrFail($orderId);

        $validatedData = $request->validated();

        $invoiceInfo = $order->invoiceInfo()->create($validatedData);

        $order->update(['invoice_status' => 'C']);
        
        return response()->json([
            'message' => 'Fatura bilgileri başarıyla oluşturuldu.',
            'invoice_info' => $invoiceInfo
        ], 201);
    }

    /**
     * Belirtilen sipariş için fatura bilgilerini güncelle.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateInvoiceRequest $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        $validatedData = $request->validated();

        $invoiceInfo = $order->invoiceInfo;

        if ($invoiceInfo) {
            $invoiceInfo->update($validatedData);
        } else {
            $invoiceInfo = $order->invoiceInfo()->create($validatedData);
        }

        return response()->json([
            'message' => 'Fatura bilgileri başarıyla güncellendi.',
            'invoice_info' => $invoiceInfo
        ], 200);
    }

   /**
     * Belirli bir siparişin fatura durumunu 'C' olarak günceller.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateInvoiceStatusToC($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Sipariş bulunamadı.'
            ], 404);
        }

        $order->invoice_status = 'C';
        $order->save();

        return response()->json([
            'message' => 'Fatura durumu başarıyla güncellendi.',
            'order' => $order
        ]);
    }
}
