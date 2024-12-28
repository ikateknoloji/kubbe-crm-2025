<?php

namespace App\Http\Controllers\V1\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductType;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Validator;

class ProductTypeController extends Controller
{
    /**
     * Tüm ürün türlerini listeleme
     */
    public function index()
    {
        $types = ProductType::with('productCategory')->get();
        return response()->json(['data' => $types], 200);
    }

    /**
     * Yeni bir ürün türü oluşturma
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_type' => 'required|string|max:255|unique:product_types,product_type',
            'product_category_id' => 'required|exists:product_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $type = ProductType::create([
            'product_type' => $request->product_type,
            'product_category_id' => $request->product_category_id,
        ]);

        return response()->json(['data' => $type, 'message' => 'Ürün türü başarıyla oluşturuldu.'], 201);
    }

    /**
     * Belirli bir ürün türünü görüntüleme
     */
    public function show($id)
    {
        $type = ProductType::with('productCategory')->find($id);

        if (!$type) {
            return response()->json(['message' => 'Ürün türü bulunamadı.'], 404);
        }

        return response()->json(['data' => $type], 200);
    }

    /**
     * Belirli bir ürün türünü güncelleme
     */
    public function update(Request $request, $id)
    {
        $type = ProductType::find($id);

        if (!$type) {
            return response()->json(['message' => 'Ürün türü bulunamadı.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'product_type' => 'required|string|max:255|unique:product_types,product_type,' ,
            'product_category_id' => 'required|exists:product_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $type->update([
            'product_type' => $request->product_type,
            'product_category_id' => $request->product_category_id,
        ]);

        return response()->json(['data' => $type, 'message' => 'Ürün türü başarıyla güncellendi.'], 200);
    }

    /**
     * Belirli bir ürün türünü silme
     */
    public function destroy($id)
    {
        $type = ProductType::find($id);

        if (!$type) {
            return response()->json(['message' => 'Ürün türü bulunamadı.'], 404);
        }

        $type->delete();

        return response()->json(['message' => 'Ürün türü başarıyla silindi.'], 200);
    }
}
