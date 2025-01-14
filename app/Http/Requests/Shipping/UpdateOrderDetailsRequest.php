<?php

namespace App\Http\Requests\Shipping;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateOrderDetailsRequest extends FormRequest
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
            'shipping_type' => 'required|in:A,G,T',
            'shipping_address.full_name' => 'required_if:shipping_type,A,G|string|max:255',
            'shipping_address.address' => 'required_if:shipping_type,A,G|string|max:500',
            'shipping_address.city' => 'required_if:shipping_type,A,G|string|max:100',
            'shipping_address.district' => 'required_if:shipping_type,A,G|string|max:100',
            'shipping_address.country' => 'required_if:shipping_type,A,G|string|max:100',
            'shipping_address.phone' => 'required_if:shipping_type,A,G|string|max:20',
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
            'shipping_type.required' => 'Gönderim türü zorunludur.',
            'shipping_type.in' => 'Gönderim türü A, G veya T olmalıdır.',
            'shipping_address.full_name.required_if' => 'Gönderim türü A veya G olduğunda tam isim zorunludur.',
            'shipping_address.full_name.string' => 'Tam isim metin olmalıdır.',
            'shipping_address.full_name.max' => 'Tam isim en fazla 255 karakter olabilir.',
            'shipping_address.address.required_if' => 'Gönderim türü A veya G olduğunda adres zorunludur.',
            'shipping_address.address.string' => 'Adres metin olmalıdır.',
            'shipping_address.address.max' => 'Adres en fazla 500 karakter olabilir.',
            'shipping_address.city.required_if' => 'Gönderim türü A veya G olduğunda şehir zorunludur.',
            'shipping_address.city.string' => 'Şehir metin olmalıdır.',
            'shipping_address.city.max' => 'Şehir en fazla 100 karakter olabilir.',
            'shipping_address.district.required_if' => 'Gönderim türü A veya G olduğunda ilçe zorunludur.',
            'shipping_address.district.string' => 'İlçe metin olmalıdır.',
            'shipping_address.district.max' => 'İlçe en fazla 100 karakter olabilir.',
            'shipping_address.country.required_if' => 'Gönderim türü A veya G olduğunda ülke zorunludur.',
            'shipping_address.country.string' => 'Ülke metin olmalıdır.',
            'shipping_address.country.max' => 'Ülke en fazla 100 karakter olabilir.',
            'shipping_address.phone.required_if' => 'Gönderim türü A veya G olduğunda telefon numarası zorunludur.',
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
