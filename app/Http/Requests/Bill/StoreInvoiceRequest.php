<?php

namespace App\Http\Requests\Bill;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreInvoiceRequest extends FormRequest
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
            'invoice_type'  => 'required|in:C,I',
            'company_name'  => 'required_if:invoice_type,I|string|max:255',
            'name'          => 'required_if:invoice_type,C|string|max:255',
            'surname'       => 'required_if:invoice_type,C|string|max:255',
            'tc_number'     => 'required_if:invoice_type,C|string|max:20',
            'address'       => 'required|string|max:500',
            'tax_office'    => 'required_if:invoice_type,I|string|max:255',
            'tax_number'    => 'required_if:invoice_type,I|string|max:255',
            'email'         => 'nullable|email|max:255',
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
            'invoice_type.required'   => 'Fatura türü seçilmelidir.',
            'invoice_type.in'         => 'Geçerli bir fatura türü seçilmelidir: C (Bireysel) veya I (Kurumsal).',
            'company_name.required_if'=> 'Kurumsal faturalarda şirket adı zorunludur.',
            'company_name.string'     => 'Şirket adı metin olmalıdır.',
            'company_name.max'        => 'Şirket adı en fazla 255 karakter olabilir.',
            'name.required_if'        => 'Bireysel faturalarda isim zorunludur.',
            'name.string'             => 'İsim metin olmalıdır.',
            'name.max'                => 'İsim en fazla 255 karakter olabilir.',
            'surname.required_if'     => 'Bireysel faturalarda soyisim zorunludur.',
            'surname.string'          => 'Soyisim metin olmalıdır.',
            'surname.max'             => 'Soyisim en fazla 255 karakter olabilir.',
            'tc_number.required_if'   => 'Bireysel faturalarda TC kimlik numarası zorunludur.',
            'tc_number.string'        => 'TC kimlik numarası metin olmalıdır.',
            'tc_number.max'           => 'TC kimlik numarası en fazla 20 karakter olabilir.',
            'address.required'        => 'Adres bilgisi zorunludur.',
            'address.string'          => 'Adres metin olmalıdır.',
            'address.max'             => 'Adres en fazla 500 karakter olabilir.',
            'tax_office.required_if'  => 'Kurumsal faturalarda vergi dairesi zorunludur.',
            'tax_office.string'       => 'Vergi dairesi metin olmalıdır.',
            'tax_office.max'          => 'Vergi dairesi en fazla 255 karakter olabilir.',
            'tax_number.required_if'  => 'Kurumsal faturalarda vergi numarası zorunludur.',
            'tax_number.string'       => 'Vergi numarası metin olmalıdır.',
            'tax_number.max'          => 'Vergi numarası en fazla 255 karakter olabilir.',
            'email.email'             => 'Geçerli bir e-posta adresi giriniz.',
            'email.max'               => 'E-posta en fazla 255 karakter olabilir.',
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
