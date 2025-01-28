<?php

namespace App\Http\Controllers\V1\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockIndexRequest;
use App\Http\Requests\Stok\StoreStockRequest;
use App\Http\Resources\Stock\StockResource;
use App\Models\Color;
use App\Models\ProductType;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;

class StockController extends Controller
{

    public function index(Request $request)
    {

    
        $productTypeId = $request->get('product_type_id') ?? null; 
        $colorId = $request->get('color_id')  ?? null; 

        $stocks = Stock::with(['productType.productCategory', 'color'])
        // ->where('quantity', '>', 0)
        ->when($productTypeId, function ($query, $productTypeId) {
            $query->where('product_type_id', '=', $productTypeId); 
        })
        ->when($colorId, function ($query, $colorId) {
            $query->where('color_id', '=', $colorId); 
        })
        ->get();
    
    
        // Tüm ürün tiplerini ve renk bilgilerini al
        $productTypes = ProductType::all();
        $colors = Color::all();


        return response()->json([
            'stocks' => StockResource::collection($stocks)->response()->getData(true),
            'product_types' => $productTypes,
            'colors' => $colors
        ], 200);
    }
    
    

    /**
     * Stoğa yeni ürün ekleme veya mevcut stoğu artırma.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreStockRequest $request)
    {
        $validated = $request->validated();


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
     * Stok miktarı 0 olan ürünleri listele (Resource + Pagination)
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
    public function zeroStock(Request $request)
    {
        $stocks = Stock::query()
            ->with(['productType.productCategory', 'color'])
            ->where('quantity', '=', 0)
            ->paginate(10); // Sayfalama ekledik
    
        return StockResource::collection($stocks)->response()->getData(true);
    }
    
    /**
     * Stok miktarı kritik seviyenin altında olan ürünleri listele (Resource + Pagination)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function lowStock(Request $request)
    {
        $criticalStockLevel = config('stock.critical_stock_level', 5); // Default değeri 5
    
        $stocks = Stock::query()
            ->with(['productType.productCategory', 'color'])
            ->where('quantity', '<', $criticalStockLevel)
            ->paginate(10); // Sayfalama ekledik
        
        return StockResource::collection($stocks)->response()->getData(true);
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
