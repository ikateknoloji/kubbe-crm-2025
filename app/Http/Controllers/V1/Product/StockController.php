<?php

namespace App\Http\Controllers\V1\Product;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;

class StockController extends Controller
{

    /**
     * Adet bilgisiyle tüm ürünleri listele (0 olmayanlar).
     * Filtreleme: product_type ve color_name
     * HTTP Method: OPTIONS
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'product_type' => 'nullable|string|max:255',
            'color_name' => 'nullable|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $productType = $request->query('product_type');
        $colorName = $request->query('color_name');
    
        $query = Stock::query()
            ->with(['productType', 'color'])
            ->where('quantity', '>', 0);
    
        if ($productType) {
            $query->whereHas('productType', function ($q) use ($productType) {
                $q->where('product_type', $productType);
            });
        }
    
        if ($colorName) {
            $query->whereHas('color', function ($q) use ($colorName) {
                $q->where('color_name', $colorName);
            });
        }
    
        $stocks = $query->paginate(10); 
        return response()->json(['data' => $stocks], 200);
    }

    /**
     * Stoğa yeni ürün ekleme veya mevcut stoğu artırma.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_type_id' => 'required|exists:product_types,id',
            'color_name' => 'required|string|max:255',
            'color_hex' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'quantity' => 'required|integer|min:0',
        ]);

        $color = Color::firstOrCreate(
            ['color_name' => $validated['color_name']],
            ['color_hex' => $validated['color_hex']]
        );

        $stock = Stock::updateOrCreate(
            [
                'product_type_id' => $validated['product_type_id'],
                'color_id' => $color->id,
            ],
            [
                'quantity' => DB::raw('quantity + ' . $validated['quantity']),
            ]
        );
        
        $stock->refresh();

        $message = $stock->wasRecentlyCreated
            ? 'Yeni stok başarıyla eklendi.'
            : 'Mevcut stok miktarı artırıldı.';

        return response()->json(['message' => $message, 'data' => $stock], 200);
    }

    /**
     * Stok güncelleme.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:0',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $stock = Stock::find($id);
    
        if (!$stock) {
            return response()->json(['message' => 'Stok kaydı bulunamadı.'], 404);
        }
    
        $stock->quantity = $request->quantity;
        $stock->save();
    
        return response()->json(['message' => 'Stok başarıyla güncellendi.', 'data' => $stock], 200);
    }

    /**
     * Stok düşürme.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function decrement(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $stock = Stock::find($id);

        if (!$stock) {
            return response()->json(['message' => 'Stok kaydı bulunamadı.'], 404);
        }

        if ($stock->quantity < $request->quantity) {
            return response()->json(['message' => 'Stok miktarı yetersiz.'], 400);
        }

        $stock->quantity -= $request->quantity;
        $stock->save();

        return response()->json(['message' => 'Stok başarıyla düşürüldü.', 'data' => $stock], 200);
    }

    /**
     * Belirli bir stoğa miktar ekleme (increment).
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function increment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $stock = Stock::find($id);

        if (!$stock) {
            return response()->json(['message' => 'Stok kaydı bulunamadı.'], 404);
        }

        $stock->quantity += $request->quantity;
        $stock->save();

        return response()->json([
            'message' => 'Stok miktarı başarıyla artırıldı.',
            'data' => $stock
        ], 200);
    }
    
    /**
     * Stok miktarı 0 olan ürünleri listele.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function zeroStock(Request $request)
    {
        $stocks = Stock::query()
            ->with(['productType', 'color']) 
            ->where('quantity', '=', 0) 
            ->get();

        return response()->json(['data' => $stocks], 200);
    }

    /**
     * Stok miktarı kritik seviyenin altında olan ürünleri listele.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function lowStock(Request $request)
    {
        $criticalStockLevel = config('stock.critical_stock_level');

        $stocks = Stock::query()
            ->with(['productType', 'color'])
            ->where('quantity', '<', $criticalStockLevel)
            ->get();
    
        // Sonuçları döndür
        return response()->json(['data' => $stocks], 200);
    }

    /**
     * Belirli bir stok kaydını tamamen sil.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Stok kaydını bul
        $stock = Stock::find($id);

        if (!$stock) {
            return response()->json(['message' => 'Stok kaydı bulunamadı.'], 404);
        }

        // Stok kaydını sil
        $stock->delete();

        return response()->json(['message' => 'Stok başarıyla silindi.'], 200);
    }
}
