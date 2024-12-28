<?php

namespace App\Http\Controllers\V1\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = ProductCategory::all();
        return response()->json(['data' => $categories], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string|unique:product_categories,category|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = ProductCategory::create([
            'category' => $request->category,
        ]);

        return response()->json(['data' => $category, 'message' => 'Kategori başarıyla oluşturuldu.'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = ProductCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Kategori bulunamadı.'], 404);
        }

        return response()->json(['data' => $category], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = ProductCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Kategori bulunamadı.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'category' => 'required|string|unique:product_categories,category,' . $id . '|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category->update([
            'category' => $request->category,
        ]);

        return response()->json(['data' => $category, 'message' => 'Kategori başarıyla güncellendi.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = ProductCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Kategori bulunamadı.'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Kategori başarıyla silindi.'], 200);
    }
}
