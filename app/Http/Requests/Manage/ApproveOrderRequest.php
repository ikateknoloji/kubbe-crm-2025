<?php

namespace App\Http\Requests\Manage;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Manage\OrderConfirmRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApproveOrderRequest extends FormRequest
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
            'order_id'    => [
                'required',
                'integer',
                'exists:orders,id',
                new OrderConfirmRule('OC')
            ],
            'paid_amount' => [
                'required',
                'numeric',
                'min:0'
            ],
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
            'order_id.required'   => 'Sipariş ID\'si zorunludur.',
            'order_id.integer'    => 'Sipariş ID\'si tamsayı olmalıdır.',
            'order_id.exists'     => 'Belirtilen sipariş bulunamadı.',
            'paid_amount.required'=> 'Ödeme miktarı zorunludur.',
            'paid_amount.numeric' => 'Ödeme miktarı sayısal bir değer olmalıdır.',
            'paid_amount.min'     => 'Ödeme miktarı en az 0 olmalıdır.',
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
