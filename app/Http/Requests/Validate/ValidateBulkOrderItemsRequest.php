<?php

namespace App\Http\Requests\Validate;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\StockValidation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidateBulkOrderItemsRequest extends FormRequest
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
        $items = $this->input('items', []);

        return [
            'items' => [
                'required',
                'array',
                new StockValidation($items)
            ],
            'items.*.stock_id' => 'required|integer|exists:stocks,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:25',
            'items.*.image' => 'required|array',
            'items.*.image.*' => 'required|url|max:255',
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
            'items.required' => 'Ürün listesi zorunludur.',
            'items.array' => 'Ürün listesi geçersiz formatta.',
            'items.*.stock_id.required' => 'Her ürün için stok ID zorunludur.',
            'items.*.stock_id.integer' => 'Stok ID tam sayı olmalıdır.',
            'items.*.stock_id.exists' => 'Seçilen stok mevcut değil.',
            'items.*.quantity.required' => 'Her ürün için miktar zorunludur.',
            'items.*.quantity.integer' => 'Miktar tam sayı olmalıdır.',
            'items.*.quantity.min' => 'Miktar en az 1 olmalıdır.',
            'items.*.unit_price.required' => 'Her ürün için birim fiyat zorunludur.',
            'items.*.unit_price.numeric' => 'Birim fiyat sayısal bir değer olmalıdır.',
            'items.*.unit_price.min' => 'Birim fiyat en az 25 olmalıdır.',
            'items.*.image.required' => 'Her ürün için resim zorunludur.',
            'items.*.image.array' => 'Resim bilgisi geçersiz formatta.',
            'items.*.image.*.required' => 'Her resim için URL gereklidir.',
            'items.*.image.*.url' => 'Resim URL\'si geçerli bir URL olmalıdır.',
            'items.*.image.*.max' => 'Resim URL\'si en fazla 255 karakter olabilir.',
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
