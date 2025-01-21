<?php

namespace App\Http\Requests\Validate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidateInvoiceRequest extends FormRequest
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
            'invoice_type' => 'required|in:C,I',
            'company_name' => 'required_if:invoice_type,I|max:255',
            'name'         => 'required_if:invoice_type,C|max:255',
            'surname'      => 'required_if:invoice_type,C|max:255',
            'tc_number'    => 'required_if:invoice_type,C|digits:11',
            'address'      => 'required|max:500',
            'tax_office'   => 'required_if:invoice_type,I|max:255',
            'tax_number'   => 'required_if:invoice_type,I|max:50',
            'email'        => 'nullable|email|max:255',
        ];
    }

    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if (empty($this->all())) {
                $validator->errors()->add('form', 'Form verileri gönderilmedi.');
            }
        });
    }

    /**
     * Özel hata mesajları.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'invoice_type.required' => 'Fatura türü seçilmelidir.',
            'invoice_type.in' => 'Geçerli bir fatura türü seçilmelidir: C (Bireysel) veya I (Kurumsal).',
            'company_name.required_if' => 'Kurumsal faturalarda şirket adı zorunludur.',
            'company_name.max' => 'Şirket adı en fazla 255 karakter olabilir.',
            'name.required_if' => 'Bireysel faturalarda isim zorunludur.',
            'name.max' => 'İsim en fazla 255 karakter olabilir.',
            'surname.required_if' => 'Bireysel faturalarda soyisim zorunludur.',
            'surname.max' => 'Soyisim en fazla 255 karakter olabilir.',
            'tc_number.required_if' => 'Bireysel faturalarda TC kimlik numarası zorunludur.',
            'tc_number.digits' => 'TC kimlik numarası 11 haneli olmalıdır.',
            'address.required' => 'Adres bilgisi zorunludur.',
            'address.max' => 'Adres en fazla 500 karakter olabilir.',
            'tax_office.required_if' => 'Kurumsal faturalarda vergi dairesi bilgisi zorunludur.',
            'tax_office.max' => 'Vergi dairesi en fazla 255 karakter olabilir.',
            'tax_number.required_if' => 'Kurumsal faturalarda vergi numarası zorunludur.',
            'tax_number.max' => 'Vergi numarası en fazla 50 karakter olabilir.',
            'email.required' => 'E-posta adresi zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.max' => 'E-posta adresi en fazla 255 karakter olabilir.',
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
