<?php

namespace App\Http\Controllers\V1\Manufacturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Manufacturer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ManufacturerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $manufacturers = Manufacturer::all();
        return response()->json([
            'success' => true,
            'data' => $manufacturers,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:manufacturers,name|max:255',
            'image' => 'required|image|max:2048',
        ], [
            'name.required' => 'Üretici adı alanı zorunludur.',
            'name.string' => 'Üretici adı yalnızca metin olmalıdır.',
            'name.unique' => 'Bu üretici adı zaten kayıtlı.',
            'name.max' => 'Üretici adı en fazla :max karakter olabilir.',
            'image.required' => 'Resim dosyası zorunludur.',
            'image.image' => 'Yüklenen dosya bir resim olmalıdır.',
            'image.max' => 'Resim dosyası boyutu en fazla :max KB olabilir.',
        ]);
    
        // Doğrulama hatası varsa
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    
        $data = $request->only(['name']);
        if ($request->hasFile('image')) {
            $data['image'] = $this->storeManufacturerImage($request->file('image'));
        }
    
        $manufacturer = Manufacturer::create($data);
    
        return response()->json([
            'success' => true,
            'message' => 'Üretici başarıyla oluşturuldu.',
            'data' => $manufacturer,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $manufacturer = Manufacturer::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $manufacturer,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $manufacturer = Manufacturer::findOrFail($id);
        $manufacturer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Üretici başarıyla silindi.',
        ], 200);
    }

    /**
     * Resmi yükler ve tam URL'sini döner.
     */
    private function storeManufacturerImage($file)
    {
        $path = $file->store('manufacturers', 'public');
        return asset('storage/' . $path);
    }
}

