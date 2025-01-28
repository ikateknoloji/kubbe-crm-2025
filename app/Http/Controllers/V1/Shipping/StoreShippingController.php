<?php

namespace App\Http\Controllers\V1\Shipping;

use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Helpers\FileUploadHelper;
use App\Helpers\OrderHelper;
use App\Models\CustomerOrder;
use App\Models\ManufacturerOrder;
use App\Models\Order;
use App\Models\OrderImage;
use App\Models\ShippingAddress;

use App\Http\Requests\Shipping\StoreOrderDetailsRequest;
use App\Http\Requests\Shipping\UpdateOrderDetailsRequest;
use App\Http\Requests\Shipping\AddOrderImageRequest;

use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Log;

class StoreShippingController extends Controller
{

    /**
     * Sipariş Resimlerini ve Adres Bilgilerini Kaydeder
     *
     * @param  \App\Http\Requests\Shipping\StoreOrderDetailsRequest  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOrderDetails(StoreOrderDetailsRequest $request, $orderId): JsonResponse
    {
        DB::beginTransaction();
        try {
            
            $order = Order::findOrFail($orderId);

            if ($order->status !== OrderStatus::P) {
                return response()->json([
                    'message' => 'Bu işlem yalnızca "Üretimde" olan siparişler için gerçekleştirilebilir.'
                ], 403);
            }

            $validated = $request->validated();

            OrderImage::insert(array_map(function ($imagePath) use ($order) {
                return [
                    'order_id'   => $order->id,
                    'image_path' => $imagePath,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $validated['images']));


            if ($validated['shipping_type'] !== 'T') {
                ShippingAddress::create(array_merge($validated['shipping_address'], ['order_id' => $order->id]));
            }

            $order->update([
                'status'        => OrderStatus::SHP,
                'shipping_type' => $validated['shipping_type']
            ]);

            OrderHelper::createManufacturerOrder($order);

            DB::commit();
            return response()->json(['message' => 'Sipariş güncellendi ve sevkiyat aşamasına geçti.'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("storeOrderDetails hata: {$e->getMessage()} [{$e->getFile()}:{$e->getLine()}]");
            return response()->json(['message' => 'Bir hata oluştu.'], 500);
        }
    }

    /**
     * Sadece Adres Bilgilerini Günceller
     *
     * @param  \App\Http\Requests\Shipping\UpdateOrderDetailsRequest  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrderDetails(UpdateOrderDetailsRequest $request, $orderId): JsonResponse
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'message' => 'Sipariş bulunamadı.',
            ], 404);
        }

        if ($order->status !== 'SHP') {
            return response()->json([
                'message' => 'Bu işlem yalnızca durumu Teslimat aşamasında olan siparişler için güncelleme yapılabilir.',
            ], 403);
        }

        // Doğrulanmış verileri al
        $validated = $request->validated();

        // Order durumunu güncelle
        $order->shipping_type = $validated['shipping_type'];
        $order->save();
        
        if ($validated['shipping_type'] !== 'T') {
            $shippingAddressData = $validated['shipping_address'];
            $shippingAddress = ShippingAddress::where('order_id', $order->id)->first();

            if ($shippingAddress) {
                $shippingAddress->update($shippingAddressData);
            } else {
                $shippingAddressData['order_id'] = $order->id;
                ShippingAddress::create($shippingAddressData);
            }
        } else {
            ShippingAddress::where('order_id', $order->id)->delete();
        }

        return response()->json([
            'message' => 'Sipariş detayları başarıyla güncellendi.',
        ], 200);
    }

    /**
     * Siparişe Tek Bir Resim Ekler
     *
     * @param  \App\Http\Requests\Shipping\AddOrderImageRequest  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function addOrderImage(AddOrderImageRequest $request, $orderId): JsonResponse
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'message' => 'Sipariş bulunamadı.',
            ], 404);
        }

        $validated = $request->validated();

        OrderImage::create([
            'order_id' => $order->id,
            'image_path' => $validated['image_url'],
        ]);

        return response()->json([
            'message' => 'Resim başarıyla eklendi.',
            'image_url' => $validated['image_url'],
        ], 201);
    }

    /**
     * Resmi Siler ve Veritabanı ile Fiziksel Dosyadan Kaldırır
     *
     * @param  int  $imageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeOrderImage(int $imageId): JsonResponse
    {
        $image = OrderImage::find($imageId);

        if (!$image) {
            return response()->json([
                'message' => 'Resim bulunamadı.',
            ], 404);
        }

        FileUploadHelper::revertFile($image->image_path);

        $image->delete();

        return response()->json([
            'message' => 'Resim başarıyla silindi.',
        ], 200);
    }


    /**
     * Sipariş Baskets ve Order Items İşlemlerini Yürütür
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    private function processOrderItems(Order $order): void
    {
        $orderBaskets = $order->orderBaskets()->with('orderItem')->get();

        if ($orderBaskets->isEmpty()) {
            abort(response()->json(['message' => 'Sipariş sepeti bulunamadı.'], 404));
        }

        $orderItems = $orderBaskets->pluck('orderItem')->filter();

        if ($orderItems->isEmpty()) {
            abort(response()->json(['message' => 'Sipariş öğesi bulunamadı.'], 404));
        }

        $totalAmount = $orderItems->sum('quantity');
        $totalPrice = $orderItems->sum(function($item) {
            return $item->unit_price * $item->quantity;
        });
        $averageUnitPrice = $totalAmount > 0 ? $totalPrice / $totalAmount : 0;

        CustomerOrder::create([
            'order_id' => $order->id,
            'average_unit_price' => $averageUnitPrice,
            'total_price' => $totalPrice,
            'total_amount' => $totalAmount,
        ]);

        ManufacturerOrder::create([
            'order_id' => $order->id,
            'total_amount' => $totalAmount,
        ]);
    }
}
