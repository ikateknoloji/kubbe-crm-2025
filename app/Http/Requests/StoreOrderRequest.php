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

            // Sipariş kalemleri (ön ek kullanılmadan)
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

            // Fatura bilgileri (ön ek kullanılmadan)
            'invoice' => 'nullable|array',
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
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Doğrulama Hataları.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
