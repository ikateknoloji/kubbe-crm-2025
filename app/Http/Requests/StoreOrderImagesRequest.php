<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderImagesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'images' => 'required|array',
            'images.*' => 'required|url|max:500',
        ];
    }

    public function messages()
    {
        return [
            'images.required' => 'Resimler alanı zorunludur.',
            'images.array' => 'Resimler bir dizi olmalıdır.',
            'images.*.required' => 'Her resim için URL zorunludur.',
            'images.*.url' => 'Geçerli bir resim URL\'si giriniz.',
            'images.*.max' => 'Resim URL\'si en fazla 500 karakter olabilir.',
        ];
    }
}
