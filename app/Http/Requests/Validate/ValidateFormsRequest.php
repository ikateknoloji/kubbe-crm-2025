<?php

namespace App\Http\Requests\Validate;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\TurkishPhoneNumber;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidateFormsRequest extends FormRequest
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
            'customer.name' => 'required|string|max:255',
            'customer.surname' => 'required|string|max:255',
            'customer.phone' => ['required', 'string', 'max:15', new TurkishPhoneNumber],
            'customer.email' => 'nullable|string|email|max:255',
            'order.order_name' => 'required|string|max:255',
            'order.note' => 'nullable|string',
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
            'customer.name.required' => 'Müşteri adı zorunludur.',
            'customer.surname.required' => 'Müşteri soyadı zorunludur.',
            'customer.phone.required' => 'Telefon numarası zorunludur.',
            'customer.phone.max' => 'Telefon numarası en fazla 15 karakter olabilir.',
            'customer.email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'order.order_name.required' => 'Sipariş adı zorunludur.',
            'order.note.string' => 'Not alanı metin olmalıdır.',
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
