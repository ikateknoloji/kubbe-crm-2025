<?php

namespace App\Http\Controllers\V1\Order;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Rules\TurkishPhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\Validate\ValidateBulkOrderItemsRequest;
use App\Http\Requests\Validate\ValidateFormsRequest;
use App\Http\Requests\Validate\ValidateInvoiceRequest;
use App\Http\Requests\Validate\ValidateOrderItemRequest;
use App\Rules\StockItemValidation;
use App\Rules\StockValidation;
use App\Rules\StockQuantity;


class StoreController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();
        $offerPrice = $this->calculateOfferPrice($validated['items']);
        $orderCode = strtoupper(uniqid('SIPARIS_'));

        try {
            DB::transaction(function () use ($validated, $offerPrice, $orderCode, &$order) {
                $order = Order::create([
                    'order_name' => $validated['order_name'],
                    'note' => $validated['note'] ?? null,
                    'offer_price' => $offerPrice,
                    'customer_id' => Auth::id(),
                    'order_code' => $orderCode,
                ]);
                
                collect($validated['items'])->each(function ($item) use ($order) {
                    $orderBasket = $order->orderBaskets()->create();
                
                    $orderBasket->orderItem()->create([
                        'stock_id' => $item['stock_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                    ]);
                
                    $orderBasket->orderLogos()->createMany(
                        collect($item['image'])->map(fn($image) => ['image' => $image])->toArray()
                    );
                });

                $order->paymentReceipts()->create([
                    'file_path' => $validated['payment_receipt_url'],
                ]);

                if (!empty($validated['invoice'])) {
                    $order->invoiceInfo()->create($validated['invoice']);
                }
            });

            return response()->json([
                'message' => 'Sipariş ve ürünler başarıyla oluşturuldu.',
                'data' => $order->load('orderBaskets.orderItem', 'paymentReceipts'),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Sipariş oluşturulurken bir hata meydana geldi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Form içeriklerinin doğrulama işlemleri.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * Örnek İstek Yapısı
     * @POST 
     * {
     *   "customer": {
     *     "name": "Ad",
     *     "surname": "Soyad",
     *     "phone": "Telefon Numarası",
     *     "email": "Email"
     *   },
     *   "order": {
     *     "order_name": "Sipariş Adı",
     *     "note": "Not"
     *   }
     * }
     */
    public function validateForms(ValidateFormsRequest $request)
    {
        return response()->json([
            'message' => 'Doğrulama başarılı',
            'data' => $request->validated(),
        ], 200);
    }

    /**
     * Sipariş ürünü ve logo doğrulama işlemleri.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * Örnek İstek Yapısı:
     * @POST 
     * {
     *   "stock_id": 1,
     *   "quantity": 1,
     *   "unit_price": 100.0,
     *   "image": "image1.jpg"
     * }
     */
    public function validateOrderItem(ValidateOrderItemRequest $request)
    {
        return response()->json([
            'message' => 'Sipariş Başarıyla Doğrulandı.',
            'data' => $request->validated(),
        ], 200);
    }

    /**
     * Sipariş ürünleri ve logoların toplu doğrulama işlemleri.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * Örnek İstek Yapısı:
     * @POST 
     * {
     *   "items": [
     *     {
     *       "stock_id": 1,
     *       "quantity": 1,
     *       "unit_price": 100.0,
     *       "image": "image1.jpg"
     *     },
     *     {
     *       "stock_id": 2,
     *       "quantity": 2,
     *       "unit_price": 200.0,
     *       "image": "image2.jpg"
     *     }
     *   ]
     * }
     */
    public function validateBulkOrderItems(ValidateBulkOrderItemsRequest $request)
    {
        return response()->json([
            'message' => 'Tüm siparişler başarıyla doğrulandı.',
            'data' => $request->validated(),
        ], 200);
    }

    /**
     * Fatura bilgilerini doğrulama fonksiyonu.
     */
    public function validateInvoice(ValidateInvoiceRequest $request)
    {
        return response()->json([
            'message' => 'Fatura bilgileri başarıyla doğrulandı!',
        ], 200);
    }
    
    private function calculateOfferPrice(array $items)
    {
        return collect($items)->reduce(function ($total, $item) {
            return $total + ($item['quantity'] * $item['unit_price']);
        }, 0);
    }
}
