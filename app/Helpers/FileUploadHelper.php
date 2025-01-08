<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Storage;

class FileUploadHelper
{
    /**
     * Dosyayı yükler ve tam URL'sini döner.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $storagePath
     * @return string
     */
    public static function uploadFile($file, string $storagePath)
    {
        $path = $file->store($storagePath, 'public');
        return asset('storage/' . $path);
    }


    /**
     * Verilen URL'yi alır ve ilgili dosyayı siler.
     *
     * @param string $url
     * @return array
     */
    public static function revertFile(string $url): array
    {
        $baseUrl = asset('storage/');
        
        // URL'nin base URL ile başlayıp başlamadığını kontrol et
        if (strpos($url, $baseUrl) !== 0) {
            return [
                'success' => false,
                'message' => 'Geçersiz URL formatı.',
            ];
        }

        // Dosya yolunu al
        $path = str_replace($baseUrl, '', $url);

        // Dosyanın mevcut olup olmadığını kontrol et
        if (!$path || !Storage::disk('public')->exists($path)) {
            return [
                'success' => false,
                'message' => "Dosya bulunamadı: {$path}",
            ];
        }

        // Dosyayı sil
        if (Storage::disk('public')->delete($path)) {
            return [
                'success' => true,
                'message' => 'Dosya başarıyla silindi.',
            ];
        }

        // Silme işlemi başarısız olursa
        return [
            'success' => false,
            'message' => 'Dosya silinemedi.',
        ];
    }
}
