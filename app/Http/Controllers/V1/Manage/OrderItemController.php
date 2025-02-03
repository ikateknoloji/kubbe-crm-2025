<?php

namespace App\Http\Controllers\V1\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Models\OrderLogo;
use Illuminate\Support\Facades\Validator;

class OrderItemController extends Controller
{
    /**
     * Sipariş kalemini silme işlemi (confirmation_text "SİPARİŞ KALEMİ SİL" olmalıdır).
     */
    public function deleteOrderItem(Request $request, $orderItemId)
    {
        $confirmationText = $request->input('confirmation_text');
        $requiredText     = 'SİPARİŞ KALEMİ SİL';

        if ($confirmationText !== $requiredText) {
            return response()->json([
                'mesaj' => 'Doğrulama metni yanlış. "SİPARİŞ KALEMİ SİL" yazmanız gerekmektedir.'
            ], 422);
        }

        try {
            $orderItem = OrderItem::findOrFail($orderItemId);
            $orderItem->delete();
            return response()->json([
                'mesaj' => 'Sipariş kalemi başarıyla silindi.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'mesaj' => 'Sipariş kalemi silinirken hata oluştu.'
            ], 500);
        }
    }

    /**
     * Yeni sipariş kalemi ekleme işlemi.
     * Bu işlem için baskets ile ilişkili siparişin sepet ID'si (order_basket_id) gereklidir.
     */
    public function addOrderItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_basket_id' => 'required|integer|exists:order_baskets,id',
            'stock_id'        => 'required|integer|exists:stocks,id',
            'quantity'        => 'required|integer|min:1',
            'unit_price'      => 'required|numeric|min:0.01',
        ], [
            'order_basket_id.required' => 'Sipariş sepeti kimliği gereklidir.',
            'order_basket_id.exists'   => 'Geçersiz sipariş sepeti kimliği.',
            'stock_id.required'        => 'Stok kimliği gereklidir.',
            'stock_id.exists'          => 'Geçersiz stok kimliği.',
            'quantity.required'        => 'Adet bilgisi gereklidir.',
            'quantity.min'             => 'Adet en az 1 olmalıdır.',
            'unit_price.required'      => 'Birim fiyat gereklidir.',
            'unit_price.numeric'       => 'Birim fiyat sayı olmalıdır.',
            'unit_price.min'           => 'Birim fiyat 0.01 ve üzerinde olmalıdır.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mesaj'   => 'Sipariş kalemi ekleme doğrulama hataları.',
                'hatalar' => $validator->errors()
            ], 422);
        }

        try {
            $orderItem = OrderItem::create($validator->validated());
            return response()->json([
                'mesaj' => 'Sipariş kalemi başarıyla eklendi.',
                'veri'  => $orderItem
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'mesaj' => 'Sipariş kalemi eklenirken hata oluştu.'
            ], 500);
        }
    }
    
    /**
     * Sipariş logosu güncelleme işlemi.
     */
    public function updateLogo(Request $request, $orderLogoId)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|url|max:500',
        ], [
            'image.required' => 'Logo resmi URL\'si gereklidir.',
            'image.url'      => 'Geçerli bir URL giriniz.',
            'image.max'      => 'Logo URL\'si en fazla 500 karakter olabilir.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mesaj'   => 'Logo güncelleme doğrulama hataları.',
                'hatalar' => $validator->errors()
            ], 422);
        }

        try {
            $orderLogo = OrderLogo::findOrFail($orderLogoId);
            $orderLogo->update($validator->validated());
            return response()->json([
                'mesaj' => 'Logo başarıyla güncellendi.',
                'veri'  => $orderLogo
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'mesaj' => 'Logo güncellenirken hata oluştu.'
            ], 500);
        }
    }
    
    /**
     * Yeni sipariş logosu ekleme işlemi.
     */
    public function addOrderLogo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_basket_id' => 'required|integer|exists:order_baskets,id',
            'image'           => 'required|url|max:500',
        ], [
            'order_basket_id.required' => 'Sipariş sepeti kimliği gereklidir.',
            'order_basket_id.exists'   => 'Geçersiz sipariş sepeti kimliği.',
            'image.required'           => 'Logo resmi URL\'si gereklidir.',
            'image.url'                => 'Geçerli bir URL giriniz.',
            'image.max'                => 'Logo URL\'si en fazla 500 karakter olabilir.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mesaj'   => 'Logo ekleme doğrulama hataları.',
                'hatalar' => $validator->errors()
            ], 422);
        }

        try {
            $orderLogo = OrderLogo::create([
                'order_basket_id' => $request->input('order_basket_id'),
                'image'           => $request->input('image'),
            ]);
            return response()->json([
                'mesaj' => 'Logo başarıyla eklendi.',
                'veri'  => $orderLogo
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'mesaj' => 'Logo eklenirken hata oluştu.'
            ], 500);
        }
    }
    
    /**
     * Sipariş logosunu silme işlemi (confirmation_text "SİPARİŞ LOGO SİL" olmalıdır).
     */
    public function deleteOrderLogo(Request $request, $orderLogoId)
    {
        $confirmationText = $request->input('confirmation_text');
        $requiredText     = 'SİPARİŞ LOGO SİL';

        if ($confirmationText !== $requiredText) {
            return response()->json([
                'mesaj' => 'Doğrulama metni yanlış. "SİPARİŞ LOGO SİL" yazmanız gerekmektedir.'
            ], 422);
        }

        try {
            $orderLogo = OrderLogo::findOrFail($orderLogoId);
            $orderLogo->delete();
            return response()->json([
                'mesaj' => 'Logo başarıyla silindi.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'mesaj' => 'Logo silinirken hata oluştu.'
            ], 500);
        }
    }


    /**
     * Mevcut sipariş kalemini güncelleme işlemi.
     * Sipariş sepeti kimliği (order_basket_id) değiştirilemez.
     */
    public function updateOrderItem(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'stock_id'   => 'required|integer|exists:stocks,id',
            'quantity'   => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0.01',
        ], [
            'stock_id.required'   => 'Stok kimliği gereklidir.',
            'stock_id.exists'     => 'Geçersiz stok kimliği.',
            'quantity.required'   => 'Adet bilgisi gereklidir.',
            'quantity.min'        => 'Adet en az 1 olmalıdır.',
            'unit_price.required' => 'Birim fiyat gereklidir.',
            'unit_price.numeric'  => 'Birim fiyat sayı olmalıdır.',
            'unit_price.min'      => 'Birim fiyat 0.01 ve üzerinde olmalıdır.',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'mesaj'   => 'Sipariş kalemi güncelleme doğrulama hataları.',
                'hatalar' => $validator->errors()
            ], 422);
        }
    
        try {
            $orderItem = OrderItem::findOrFail($id);
        
            // order_basket_id güncellenmez
            $orderItem->update([
                'stock_id'   => $request->stock_id,
                'quantity'   => $request->quantity,
                'unit_price' => $request->unit_price,
            ]);
        
            return response()->json([
                'mesaj' => 'Sipariş kalemi başarıyla güncellendi.',
                'veri'  => $orderItem
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'mesaj' => 'Sipariş kalemi güncellenirken hata oluştu.'
            ], 500);
        }
    }
}
