<?php

namespace App\Http\Controllers\V1\Product;

use App\Http\Controllers\Controller;
use App\Rules\HexColor;
use Illuminate\Http\Request;
use App\Models\Color;
use Illuminate\Support\Facades\Validator;

class ColorController extends Controller
{
    /**
     * Tüm renkleri listeleme
     */
    public function index()
    {
        $colors = Color::all();
        return response()->json(['data' => $colors], 200);
    }

    /**
     * Yeni bir renk oluşturma
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'color_name' => 'required|string|max:255|unique:colors,color_name',
            'color_hex' => 'required|string|max:7|unique:colors,color_hex|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $color = Color::create([
            'color_name' => $request->color_name,
            'color_hex' => $request->color_hex,
        ]);

        return response()->json(['data' => $color, 'message' => 'Renk başarıyla oluşturuldu.'], 201);
    }

    /**
     * Belirli bir rengi görüntüleme
     */
    public function show($id)
    {
        $color = Color::find($id);

        if (!$color) {
            return response()->json(['message' => 'Renk bulunamadı.'], 404);
        }

        return response()->json(['data' => $color], 200);
    }

    /**
     * Belirli bir rengi güncelleme
     */
    public function update(Request $request, $id)
    {
        $color = Color::find($id);

        if (!$color) {
            return response()->json(['message' => 'Renk bulunamadı.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'color_name' => 'required|string|max:255|unique:colors,color_name',
            'color_hex' => ['required', 'string', 'max:7', 'unique:colors,color_hex', new HexColor],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $color->update([
            'color_name' => $request->color_name,
            'color_hex' => $request->color_hex,
        ]);

        return response()->json(['data' => $color, 'message' => 'Renk başarıyla güncellendi.'], 200);
    }

    /**
     * Belirli bir rengi silme
     */
    public function destroy($id)
    {
        $color = Color::find($id);

        if (!$color) {
            return response()->json(['message' => 'Renk bulunamadı.'], 404);
        }

        $color->delete();

        return response()->json(['message' => 'Renk başarıyla silindi.'], 200);
    }
}
