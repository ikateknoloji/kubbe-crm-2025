<?php

namespace App\Http\Controllers\V1\Image;

use App\Http\Controllers\Controller;
use App\Http\Requests\Image\RevertOrderLogoRequest;
use App\Http\Requests\Image\RevertPaymentReceiptRequest;
use App\Http\Requests\Image\RevertShippingImageRequest;
use App\Helpers\FileUploadHelper;
use Illuminate\Http\JsonResponse;

class RevertImageController extends Controller
{
    /**
     * Yüklenen bir sipariş logosunu siler.
     *
     * @param \App\Http\Requests\Image\RevertOrderLogoRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function revertOrderLogo(RevertOrderLogoRequest $request): JsonResponse
    {
        $url = $request->input('image_url');
        $result = FileUploadHelper::revertFile($url);

        return response()->json([
            'message' => $result['message'],
        ], $result['success'] ? 200 : 404);
    }

    /**
     * Yüklenen bir fatura bilgisini siler.
     *
     * @param \App\Http\Requests\Image\RevertPaymentReceiptRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function revertPaymentReceipt(RevertPaymentReceiptRequest $request): JsonResponse
    {
        $url = $request->input('payment_receipt_url');
        $result = FileUploadHelper::revertFile($url);

        return response()->json([
            'message' => $result['message'],
        ], $result['success'] ? 200 : 404);
    }

    /**
     * Yüklenen bir sipariş resmini siler.
     *
     * @param \App\Http\Requests\Image\RevertShippingImageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function revertShippingImage(RevertShippingImageRequest $request): JsonResponse
    {
        $url = $request->input('order_image_url');
        $result = FileUploadHelper::revertFile($url);

        return response()->json([
            'message' => $result['message'],
        ], $result['success'] ? 200 : 404);
    }
}
