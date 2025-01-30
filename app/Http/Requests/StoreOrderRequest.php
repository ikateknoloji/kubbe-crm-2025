<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\StockQuantity;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Sipariş bilgileri (order ile ilgili)
            'order_name' => 'required|string|max:255',
            'note' => 'nullable|string',
            'payment_receipt_url' => 'required|url',
            'shipping_type' => 'required|in:A,G,T',

            // Sipariş kalemleri
            'items' => [
                'required',
                'array',
                new StockQuantity($this->input('items')),
            ],
            'items.*.stock_id'   => 'required|integer|exists:stocks,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:25',
            'items.*.image'      => 'required|array',
            'items.*.image.*'    => 'required|url|max:255',

            // Fatura bilgileri
            'invoice'              => 'required|array',
            'invoice.invoice_type' => 'required_with:invoice|in:C,I',
            'invoice.company_name' => 'required_if:invoice.invoice_type,I|max:255',
            'invoice.name'         => 'required_if:invoice.invoice_type,C|max:255',
            'invoice.surname'      => 'required_if:invoice.invoice_type,C|max:255',
            'invoice.tc_number'    => 'required_if:invoice.invoice_type,C|digits:11',
            'invoice.address'      => 'required_with:invoice|max:500',
            'invoice.tax_office'   => 'required_if:invoice.invoice_type,I|max:255',
            'invoice.tax_number'   => 'required_if:invoice.invoice_type,I|max:50',
            'invoice.email'        => 'nullable|max:255',

            // Müşteri bilgileri
            'customer' => 'required|array',
            'customer.name'  => 'required|string|max:255',
            'customer.email' => 'nullable|max:255',
            'customer.phone' => 'required|string|max:255',

            // Gönderim adresi (yalnızca "A" veya "G" seçildiğinde zorunlu)
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
            'shipping_type.in' => 'Gönderim tipi yalnızca "A" (Alıcı Ödemeli), "G" (Gönderici Ödemeli) veya "T" (Ofis Teslim) olabilir.',

            'shipping_address.required_if' => 'Teslimat adresi yalnızca "Alıcı Ödemeli" veya "Gönderici Ödemeli" seçildiğinde gereklidir.',
            'shipping_address.array' => 'Teslimat adresi geçerli bir formatta olmalıdır.',

            'shipping_address.full_name.required_if' => 'Alıcı adı yalnızca "Alıcı Ödemeli" veya "Gönderici Ödemeli" seçildiğinde gereklidir.',
            'shipping_address.full_name.string' => 'Alıcı adı geçerli bir metin olmalıdır.',
            'shipping_address.full_name.max' => 'Alıcı adı en fazla 200 karakter olabilir.',

            'shipping_address.address.required_if' => 'Adres yalnızca "Alıcı Ödemeli" veya "Gönderici Ödemeli" seçildiğinde gereklidir.',
            'shipping_address.address.string' => 'Adres geçerli bir metin olmalıdır.',
            'shipping_address.address.max' => 'Adres en fazla 500 karakter olabilir.',

            'shipping_address.city.required_if' => 'Şehir yalnızca "Alıcı Ödemeli" veya "Gönderici Ödemeli" seçildiğinde gereklidir.',
            'shipping_address.city.string' => 'Şehir geçerli bir metin olmalıdır.',
            'shipping_address.city.max' => 'Şehir en fazla 100 karakter olabilir.',

            'shipping_address.district.required_if' => 'İlçe yalnızca "Alıcı Ödemeli" veya "Gönderici Ödemeli" seçildiğinde gereklidir.',
            'shipping_address.district.string' => 'İlçe geçerli bir metin olmalıdır.',
            'shipping_address.district.max' => 'İlçe en fazla 100 karakter olabilir.',

            'shipping_address.country.required_if' => 'Ülke yalnızca "Alıcı Ödemeli" veya "Gönderici Ödemeli" seçildiğinde gereklidir.',
            'shipping_address.country.string' => 'Ülke geçerli bir metin olmalıdır.',
            'shipping_address.country.max' => 'Ülke en fazla 100 karakter olabilir.',

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
            'message' => 'Doğrulama Hataları.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
