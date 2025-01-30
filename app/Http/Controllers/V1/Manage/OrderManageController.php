<?php

namespace App\Http\Controllers\V1\Manage;

use App\Enums\OrderStatus;

use App\Helpers\FileUploadHelper;
use App\Helpers\OrderHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Manage\ApproveOrderRequest;
use App\Http\Requests\Manage\PrepareForShippingRequest;
use App\Http\Requests\Manage\AssignManufacturerRequest;
use App\Http\Requests\StoreOrderImagesRequest;
use App\Models\Order;
use App\Models\Manufacturer;
use App\Models\OrderImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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

        $order->timeline()->updateOrCreate(
            ['order_id' => $order->id],
            ['production_started_at' => now()]
        );
        
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

    /**
     * Sipariş için resim yükler ve kaydeder.
     *
     * @param    $request
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOrderImages(StoreOrderImagesRequest $request, $orderId): JsonResponse
    {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($orderId);
        
            $validated = $request->validated();
            $uploadedImages = [];

            foreach ($validated['images'] as $image) {
                $filePath = FileUploadHelper::uploadFile($image, 'order_images');
            
                $uploadedImages[] = [
                    'order_id'   => $order->id,
                    'image_path' => $filePath,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        
            $order->update(['status' => OrderStatus::SHP]);
            $order->timeline()->update(['production_completed_at' => now()]);

            OrderImage::insert($uploadedImages);
            OrderHelper::createManufacturerOrder($order);

            DB::commit();
            return response()->json([
                'message' => 'Sipariş resimleri başarıyla yüklendi.',
                'uploaded_images' => $uploadedImages
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Bir hata oluştu.', 'error' => $e->getMessage()], 500);
        }
    }
}
