<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StockIndexRequest extends FormRequest
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
            'product_type' => 'nullable|string|max:255',
            'color_name' => 'nullable|string|max:255',
        ];
    }

    /**
     * Doğrulama hatası mesajları.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'product_type.string' => 'Ürün tipi metin olmalıdır.',
            'product_type.max' => 'Ürün tipi en fazla 255 karakter olabilir.',
            'color_name.string' => 'Renk adı metin olmalıdır.',
            'color_name.max' => 'Renk adı en fazla 255 karakter olabilir.',
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
