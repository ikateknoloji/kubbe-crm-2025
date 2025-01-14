<?php

namespace App\Http\Requests\Shipping;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOrderDetailsRequest extends FormRequest
{
    /**
     * İstek yetkilendirmesi.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Gerekirse yetkilendirme mantığını buraya ekleyin.
    }

    /**
     * Doğrulama kuralları.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'images' => 'required|array',
            'images.*' => 'required|url|max:500',
            'shipping_type' => 'required|in:A,G,T',
            'shipping_address' => 'required_if:shipping_type,A,G|array',
            'shipping_address.full_name' => 'required_with:shipping_address|string|max:255',
            'shipping_address.address' => 'required_with:shipping_address|string|max:500',
            'shipping_address.city' => 'required_with:shipping_address|string|max:100',
            'shipping_address.district' => 'required_with:shipping_address|string|max:100',
            'shipping_address.country' => 'required_with:shipping_address|string|max:100',
            'shipping_address.phone' => 'required_with:shipping_address|string|max:20',
        ];
    }

    /**
     * Özel hata mesajları.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'images.required' => 'Resim dizisi zorunludur.',
            'images.array' => 'Resimler bir dizi olmalıdır.',
            'images.*.required' => 'Her resim için URL zorunludur.',
            'images.*.url' => 'Resim URL\'si geçerli bir URL olmalıdır.',
            'images.*.max' => 'Her resim URL\'si en fazla 500 karakter olabilir.',
            'shipping_type.required' => 'Gönderim türü zorunludur.',
            'shipping_type.in' => 'Gönderim türü A, G veya T olmalıdır.',
            'shipping_address.required_if' => 'Gönderim türü A veya G olduğunda adres bilgisi zorunludur.',
            'shipping_address.array' => 'Gönderim adresi bir dizi olmalıdır.',
            'shipping_address.full_name.required_with' => 'Gönderim adresi için tam isim zorunludur.',
            'shipping_address.full_name.string' => 'Tam isim metin olmalıdır.',
            'shipping_address.full_name.max' => 'Tam isim en fazla 255 karakter olabilir.',
            'shipping_address.address.required_with' => 'Gönderim adresi zorunludur.',
            'shipping_address.address.string' => 'Adres metin olmalıdır.',
            'shipping_address.address.max' => 'Adres en fazla 500 karakter olabilir.',
            'shipping_address.city.required_with' => 'Şehir bilgisi zorunludur.',
            'shipping_address.city.string' => 'Şehir metin olmalıdır.',
            'shipping_address.city.max' => 'Şehir en fazla 100 karakter olabilir.',
            'shipping_address.district.required_with' => 'İlçe bilgisi zorunludur.',
            'shipping_address.district.string' => 'İlçe metin olmalıdır.',
            'shipping_address.district.max' => 'İlçe en fazla 100 karakter olabilir.',
            'shipping_address.country.required_with' => 'Ülke bilgisi zorunludur.',
            'shipping_address.country.string' => 'Ülke metin olmalıdır.',
            'shipping_address.country.max' => 'Ülke en fazla 100 karakter olabilir.',
            'shipping_address.phone.required_with' => 'Telefon numarası zorunludur.',
            'shipping_address.phone.string' => 'Telefon numarası metin olmalıdır.',
            'shipping_address.phone.max' => 'Telefon numarası en fazla 20 karakter olabilir.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Doğrulama Hataları.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
