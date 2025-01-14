<?php

namespace App\Http\Requests\Stok;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreStockRequest extends FormRequest
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
            'product_type_id' => 'required|exists:product_types,id',
            'color_name' => 'required|string|max:255',
            'color_hex' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'quantity' => 'required|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'product_type_id.required' => 'Ürün tipi seçilmelidir.',
            'product_type_id.exists' => 'Seçilen ürün tipi mevcut değil.',
            'color_name.required' => 'Renk adı zorunludur.',
            'color_name.string' => 'Renk adı geçerli bir metin olmalıdır.',
            'color_name.max' => 'Renk adı 255 karakteri aşamaz.',
            'color_hex.regex' => 'Renk kodu geçerli bir HEX formatında olmalıdır.',
            'quantity.required' => 'Miktar zorunludur.',
            'quantity.integer' => 'Miktar tam sayı olmalıdır.',
            'quantity.min' => 'Miktar en az 0 olabilir.',
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
