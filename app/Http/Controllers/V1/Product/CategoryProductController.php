<?php

namespace App\Http\Controllers\V1\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\Stock\StockResource;
use App\Models\Color;
use Illuminate\Http\Request;

use App\Models\ProductCategory;
use App\Models\ProductType;
use App\Models\Stock;

class CategoryProductController extends Controller
{
    /**
     * Tüm kategorileri listele.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCategories()
    {
        $categories = ProductCategory::all();
        $colors = Color::all();

        return response()->json([
            'data' => [
                'categories' => $categories,
                'colors'     => $colors,
            ]
        ], 200);
    }

    /**
     * Kategori ID ile ilişkili product_type değerlerini getir.
     * 
     * @param int $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductTypesByCategory($categoryId)
    {
        $category = ProductCategory::with('productTypes')->find($categoryId);

        if (!$category) {
            return response()->json(['message' => 'Kategori bulunamadı.'], 404);
        }

        return response()->json(['data' => $category->productTypes], 200);
    }

    /**
     * Belirli bir product_type ve color ile ilişkili stok bilgilerini getir.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStockByProductTypeAndColor(Request $request)
    {
        $validated = $request->validate([
            'product_type' => 'required|string',
            'color_name' => 'required|string',
        ]);

    
        $stock = Stock::whereRelation('productType', 'product_type', $validated['product_type'])
                      ->whereRelation('color', 'color_name', $validated['color_name'])
                      ->with(['productType', 'color'])
                      ->firstOrFail();

        return response()->json(new StockResource($stock), 200);
    }
}
