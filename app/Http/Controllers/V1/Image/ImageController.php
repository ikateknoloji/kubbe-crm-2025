<?php

namespace App\Http\Controllers\V1\Image;

use App\Http\Controllers\Controller;
use App\Http\Requests\Image\UploadOrderLogoRequest;
use App\Http\Requests\Image\UploadPaymentReceiptRequest;
use App\Http\Requests\Image\UploadShippingImageRequest;
use App\Helpers\FileUploadHelper;
use Illuminate\Http\JsonResponse;

class ImageController extends Controller
{
    /**
     * Resmi yükler ve tam URL'sini döner.
     *
     * @param \App\Http\Requests\Image\UploadOrderLogoRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadOrderLogo(UploadOrderLogoRequest $request): JsonResponse
    {
        $path = FileUploadHelper::uploadFile($request->file('image'), 'orders');

        return response()->json([
            'message' => 'Resim başarıyla yüklendi.',
            'image_url' => $path,
        ], 200);
    }
    
    /**
     * Fatura bilgisini yükler ve tam URL'sini döner.
     *
     * @param \App\Http\Requests\Image\UploadPaymentReceiptRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPaymentReceipt(UploadPaymentReceiptRequest $request): JsonResponse
    {
        $path = FileUploadHelper::uploadFile($request->file('payment_receipt_url'), 'payment_receipts');

        return response()->json([
            'message' => 'Fatura bilgisi başarıyla yüklendi.',
            'payment_receipt_url' => $path,
        ], 200);
    }


    /**
     * Resim Yükler ve Tam URL'sini Döner
     * 
     * @param \App\Http\Requests\Image\UploadShippingImageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadShippingImage(UploadShippingImageRequest $request): JsonResponse
    {
        $path = FileUploadHelper::uploadFile($request->file('order_image'), 'order_image');
        
        return response()->json([
            'message' => 'Resim başarıyla yüklendi.',
            'order_image_url' => asset('storage/' . $path),
        ], 200);
    }
}
