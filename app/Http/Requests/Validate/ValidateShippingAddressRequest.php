<?php

namespace App\Http\Requests\Validate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidateShippingAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'shipping_type' => 'required|in:A,G,T',
            'shipping_address' => 'required_if:shipping_type,A,G|array',
            'shipping_address.full_name' => 'required_if:shipping_type,A,G|string|max:200',
            'shipping_address.address' => 'required_if:shipping_type,A,G|string|max:500',
            'shipping_address.city' => 'required_if:shipping_type,A,G|string|max:100',
            'shipping_address.district' => 'required_if:shipping_type,A,G|string|max:100',
            'shipping_address.country' => 'required_if:shipping_type,A,G|string|max:100',
            'shipping_address.phone' => 'required_if:shipping_type,A,G|string|max:20',
        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages()
    {
        return [
            'shipping_type.required' => 'Gönderim tipi seçilmelidir.',
            'shipping_type.in' => 'Gönderim tipi sadece "A" (Alıcı Ödemeli), "G" (Gönderici Ödemeli) veya "T" (Ofis Teslim) olabilir.',

            'shipping_address.required_if' => 'Teslimat adresi yalnızca "Alıcı Ödemeli" veya "Gönderici Ödemeli" seçildiğinde gereklidir.',
            'shipping_address.array' => 'Teslimat adresi geçerli bir formatta olmalıdır.',

            'shipping_address.full_name.required_if' => 'Alıcı adı ve soyadı yalnızca "Alıcı Ödemeli" veya "Gönderici Ödemeli" seçildiğinde gereklidir.',
            'shipping_address.full_name.string' => 'Alıcı adı ve soyadı geçerli bir metin olmalıdır.',
            'shipping_address.full_name.max' => 'Alıcı adı en fazla 200 karakter olabilir.',

            'shipping_address.address.required_if' => 'Adres yalnızca "Alıcı Ödemeli" veya "Gönderici Ödemeli" seçildiğinde gereklidir.',
            'shipping_address.address.string' => 'Adres geçerli bir metin olmalıdır.',
            'shipping_address.address.max' => 'Adres en fazla 500 karakter olabilir.',

            'shipping_address.city.required_if' => 'Şehir bilgisi yalnızca "Alıcı Ödemeli" veya "Gönderici Ödemeli" seçildiğinde gereklidir.',
            'shipping_address.city.string' => 'Şehir bilgisi geçerli bir metin olmalıdır.',
            'shipping_address.city.max' => 'Şehir adı en fazla 100 karakter olabilir.',

            'shipping_address.district.required_if' => 'İlçe bilgisi yalnızca "Alıcı Ödemeli" veya "Gönderici Ödemeli" seçildiğinde gereklidir.',
            'shipping_address.district.string' => 'İlçe bilgisi geçerli bir metin olmalıdır.',
            'shipping_address.district.max' => 'İlçe adı en fazla 100 karakter olabilir.',

            'shipping_address.country.required_if' => 'Ülke bilgisi yalnızca "Alıcı Ödemeli" veya "Gönderici Ödemeli" seçildiğinde gereklidir.',
            'shipping_address.country.string' => 'Ülke bilgisi geçerli bir metin olmalıdır.',
            'shipping_address.country.max' => 'Ülke adı en fazla 100 karakter olabilir.',

            'shipping_address.phone.required_if' => 'Telefon numarası yalnızca "Alıcı Ödemeli" veya "Gönderici Ödemeli" seçildiğinde gereklidir.',
            'shipping_address.phone.string' => 'Telefon numarası geçerli bir formatta olmalıdır.',
            'shipping_address.phone.max' => 'Telefon numarası en fazla 20 karakter olabilir.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Adres doğrulama işlemi başarısız oldu.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
