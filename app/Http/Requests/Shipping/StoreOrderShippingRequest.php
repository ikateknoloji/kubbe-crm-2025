<?php

namespace App\Http\Requests\Shipping;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOrderShippingRequest extends FormRequest
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
            'tracking_code' => 'required|string|max:50|unique:order_shippings,tracking_code',
            'shipping_company' => 'required|string|max:100',
        ];
    }

    public function messages()
    {
        return [
            'tracking_code.required' => 'Kargo takip kodu zorunludur.',
            'tracking_code.string' => 'Kargo takip kodu bir metin olmalıdır.',
            'tracking_code.max' => 'Kargo takip kodu en fazla 50 karakter olabilir.',
            'tracking_code.unique' => 'Bu kargo takip kodu zaten kullanılmış.',
            'shipping_company.required' => 'Kargo şirketi adı zorunludur.',
            'shipping_company.string' => 'Kargo şirketi adı bir metin olmalıdır.',
            'shipping_company.max' => 'Kargo şirketi adı en fazla 100 karakter olabilir.',
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
