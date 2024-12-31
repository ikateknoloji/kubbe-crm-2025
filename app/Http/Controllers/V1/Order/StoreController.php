<?php

namespace App\Http\Controllers\V1\Order;

use App\Http\Controllers\Controller;
use App\Rules\TurkishPhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\Order;

use App\Rules\StockItemValidation;
use App\Rules\StockValidation;
use App\Rules\StockQuantity;


class StoreController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_name' => 'required|string|max:255',
            'note' => 'nullable|string',
            'items' => [
                'required',
                'array',
                new StockQuantity($request->input('items')),
            ],
            'items.*.stock_id' => 'required|integer|exists:stocks,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:25',
            'items.*.image' => 'required|array',
            'items.*.image.*' => 'required|url|max:255',
            'payment_receipt_url' => 'required|url',
            'invoice' => 'nullable|array',
            'invoice.invoice_type' => 'required_with:invoice|in:C,I',
            'invoice.company_name' => 'required_if:invoice.invoice_type,I|max:255',
            'invoice.name' => 'required_if:invoice.invoice_type,C|max:255',
            'invoice.surname' => 'required_if:invoice.invoice_type,C|max:255',
            'invoice.tc_number' => 'required_if:invoice.invoice_type,C|digits:11',
            'invoice.address' => 'required_with:invoice|max:500',
            'invoice.tax_office' => 'required_if:invoice.invoice_type,I|max:255',
            'invoice.tax_number' => 'required_if:invoice.invoice_type,I|max:50',
            'invoice.email' => 'required_with:invoice|email|max:255',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }        
    
        $validated = $validator->validated();
        $offerPrice = $this->calculateOfferPrice($validated['items']);
        $orderCode = strtoupper(uniqid('SIPARIS_'));
    

        $order = Order::create([
            'order_name' => $validated['order_name'],
            'note' => $validated['note'] ?? null,
            'offer_price' => $offerPrice,
            'customer_id' => Auth::id(),
            'order_code' => $orderCode,
        ]);
    
        collect($validated['items'])->each(function ($item) use ($order) {
            $orderBasket = $order->orderBaskets()->create();

            $orderBasket->orderItems()->create([
                'stock_id' => $item['stock_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ]);

            collect($item['image'])->each(function ($image) use ($orderBasket) {
                $orderBasket->orderLogos()->create([
                    'image' => $image,
                ]);
            });
        });
    
        $order->paymentReceipts()->create([
            'file_path' => $validated['payment_receipt_url'],
        ]); 

        if (!empty($validated['invoice'])) {
            $order->invoiceInfo()->create($validated['invoice']);
        }

        return response()->json([
            'message' => 'Sipariş ve ürünler başarıyla oluşturuldu.',
            'data' => $order->load('orderBaskets.orderItems', 'paymentReceipts'),
        ], 201);
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
    public function validateForms(Request $request)
    {
        $rules = [
            'customer.name' => 'required|string|max:255',
            'customer.surname' => 'required|string|max:255',
            'customer.phone' => ['required', 'string', 'max:15', new TurkishPhoneNumber],
            'customer.email' => 'nullable|string|email|max:255',
            'order.order_name' => 'required|string|max:255',
            'order.note' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return response()->json(['message' => 'Doğrulama başarılı', 'data' => $validator->validated()], 200);
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
    public function validateOrderItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.stock_id' => 'required|integer|exists:stocks,id',
            'items.quantity' => [
                'required',
                'integer',
                'min:1',
                new StockItemValidation($request->input('items.stock_id')),
            ],
            'items.unit_price' => 'required|numeric|min:25',
            'items.image' => 'required|array',
            'items.image.*' => 'required|url|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return response()->json([
            'message' => 'Sipariş Başarıyla Doğrulandı.',
            'data' =>  $validator->validated(), 
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
    public function validateBulkOrderItems(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => [
                'required',
                'array',
                new StockValidation($request->input('items'))
            ],
            'items.*.stock_id' => 'required|integer|exists:stocks,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:25',
            'items.*.image' => 'required|array',
            'items.*.image.*' => 'required|url|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        return response()->json([
            'message' => 'Tüm siparişler başarıyla doğrulandı.',
            'data' => $validator->validated()
        ], 200);
    }

    /**
     * Fatura bilgilerini doğrulama fonksiyonu.
     */
    public function validateInvoice(Request $request)
    {
        $rules = [
            'invoice_type' => 'required|in:C,I',
            'company_name' => 'required_if:invoice_type,I|max:255',
            'name'         => 'required_if:invoice_type,C|max:255',
            'surname'      => 'required_if:invoice_type,C|max:255',
            'tc_number'    => 'required_if:invoice_type,C|digits:11',
            'address'      => 'required|max:500',
            'tax_office'   => 'required_if:invoice_type,I|max:255',
            'tax_number'   => 'required_if:invoice_type,I|max:50',
            'email'        => 'required|email|max:255',
        ];

        $messages = [
            'invoice_type.required'  => 'Fatura türü seçilmelidir.',
            'invoice_type.in'        => 'Geçerli bir fatura türü seçilmelidir: C (Bireysel) veya I (Kurumsal).',
            'company_name.required_if' => 'Kurumsal faturalarda şirket adı zorunludur.',
            'name.required_if'       => 'Bireysel faturalarda isim zorunludur.',
            'surname.required_if'    => 'Bireysel faturalarda soyisim zorunludur.',
            'tc_number.required_if'  => 'Bireysel faturalarda TC kimlik numarası zorunludur.',
            'tc_number.digits'       => 'TC kimlik numarası 11 haneli olmalıdır.',
            'address.required'       => 'Adres bilgisi zorunludur.',
            'tax_office.required_if' => 'Kurumsal faturalarda vergi dairesi bilgisi zorunludur.',
            'tax_number.required_if' => 'Kurumsal faturalarda vergi numarası zorunludur.',
            'email.required'         => 'E-posta adresi zorunludur.',
            'email.email'            => 'Geçerli bir e-posta adresi giriniz.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        return response()->json([
            'message' => 'Fatura bilgileri başarıyla doğrulandı!',
        ], 200);
    }

    /**
     * Resmi yükler ve tam URL'sini döner.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadOrderImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $path = $request->file('image')->store('orders', 'public');

        return response()->json([
            'image' => asset('storage/' . $path),
        ], 200);
    }

    /**
     * Sipariş için yüklenen resmi siler.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * Örnek İstek Yapısı:
     * @POST
     * {
     *   "image": "http://your-domain.com/storage/orders/unique-filename.jpg"
     * }
     */
    public function deleteOrderImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $path = str_replace(asset('storage') . '/', '', $request->input('image'));

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return response()->json(['message' => 'Resim başarıyla silindi.'], 200);
        }

        return response()->json(['error' => 'Resim bulunamadı.'], 404);
    }

    /**
     * Fatura bilgisini yükler ve tam URL'sini döner.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPaymentReceipt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_receipt_url' => 'required|file|mimes:jpeg,png,jpg,gif,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $path = $request->file('payment_receipt_url')->store('payment_receipts', 'public');

        return response()->json([
            'payment_receipt_url' => asset('storage/' . $path),
        ], 200);
    }

    /**
     * Yüklenen fatura bilgisini siler.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function revertPaymentReceipt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_receipt_url' => 'required|url',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
    
        $filePath = str_replace(asset('storage/') . '/', '', $request->input('payment_receipt_url'));
    
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
            return response()->json([
                'message' => 'Fatura bilgisi başarıyla silindi.',
            ], 200);
        }
    
        return response()->json([
            'message' => 'Fatura bilgisi bulunamadı.',
        ], 404);
    }
    
    private function calculateOfferPrice(array $items)
    {
        return collect($items)->reduce(function ($total, $item) {
            return $total + ($item['quantity'] * $item['unit_price']);
        }, 0);
    }
}
