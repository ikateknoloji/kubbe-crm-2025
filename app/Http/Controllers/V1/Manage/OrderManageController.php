<?php

namespace App\Http\Controllers\V1\Manage;

use App\Enums\OrderStatus;

use App\Helpers\FileUploadHelper;
use App\Helpers\StockHelper;
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
use App\Http\Requests\Shipping\StoreOrderShippingRequest;

class OrderManageController extends Controller
{
    /**
     * Siparişi onaylayarak PRP aşamasına gönderir ve stoktan düşer.
     *
     * @param \App\Http\Requests\Manage\ApproveOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveOrder(ApproveOrderRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $order = Order::where('id', $request->input('order_id'))
                          ->with(['orderItems.stock'])
                          ->firstOrFail();

            StockHelper::reduceStockForOrder($order); 

            $order->update([
                'status' => 'PRP',
                'paid_amount' => $request->input('paid_amount'),
            ]);

            $order->timeline()->updateOrCreate(
                ['order_id' => $order->id], 
                ['approved_at' => now()]
            );

            DB::commit();

            return response()->json([
                'message' => "Sipariş '{$order->order_name}' başarıyla onaylandı ve stok güncellendi.",
                'data' => $order->load('orderItems.stock')
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Sipariş onaylanırken hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
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

        $order->timeline()->update(['production_started_at' => now()]);
        

        $manufacturer = Manufacturer::findOrFail($manufacturerId);

        return response()->json([
            'message' => "Sipariş '{$order->order_name}' başarıyla '{$manufacturer->name}' üreticisine atandı ve 'P' aşamasına getirildi."
        ], 200);
    }

    /**
     * Sipariş için resim yükler ve kaydeder.
     *
     * @param    StoreOrderImagesRequest $request
     * @param    int $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOrderImages(StoreOrderImagesRequest $request, $orderId): JsonResponse
    {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($orderId);

            $validated = $request->validated();
            $uploadedImages = [];

            foreach ($validated['images'] as $imageUrl) {
                $uploadedImages[] = [
                    'order_id'   => $order->id,
                    'image_path' => $imageUrl,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $order->update(['status' => OrderStatus::SHP]);
            $order->timeline()->update(['production_completed_at' => now()]);

            OrderImage::insert($uploadedImages);
            OrderHelper::createManufacturerOrder($order);
            OrderHelper::createCustomerOrder($order);

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

    /**
     * Sipariş için kargo bilgilerini kaydeder veya günceller.
     *
     * @param  \App\Http\Requests\Shipping\StoreOrderShippingRequest  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOrderShipping(StoreOrderShippingRequest $request, $orderId): JsonResponse
    {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($orderId);
            $validated = $request->validated();

            $order->shipping()->updateOrCreate(
                ['order_id' => $order->id],
                [
                    'tracking_code' => $validated['tracking_code'],
                    'shipping_company' => $validated['shipping_company'],
                ]
            );

            $order->update(['status' => 'PD']);
            $order->timeline()->update(['shipped_at' => now()]);


            DB::commit();
            return response()->json([
                'message' => "Sipariş '{$order->order_name}' için kargo bilgileri kaydedildi.",
                'data' => $order->shipping
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Kargo bilgileri kaydedilirken bir hata oluştu.', 'error' =>  $e->getMessage()], 500);
        }
    }

    /**
     * Siparişin durumunu günceller ancak kargo bilgisi kaydetmez.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOfficeDelivery($orderId): JsonResponse
    {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($orderId);

            // Sadece sipariş durumunu ve zaman çizelgesini güncelle
            $order->update(['status' => 'PD']);
            $order->timeline()->update(['shipped_at' => now()]);

            DB::commit();
            return response()->json([
                'message' => "Sipariş '{$order->order_name}' için kargo süreci başlatıldı ancak herhangi bir kargo bilgisi  kaydedilmedi.",
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'İşlem sırasında bir hata oluştu.', 'error' =>  $e->getMessage()],  500);
        }
    }

}
