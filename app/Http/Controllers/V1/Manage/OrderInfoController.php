<?php

namespace App\Http\Controllers\V1\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;

class OrderInfoController extends Controller
{
    // Siparişin silinmesi (confirmation_text "SİL ONAY" olmalıdır)
    public function destroy(Request $request, $orderId)
    {
        $confirmationText = $request->input('confirmation_text');
        $requiredText     = 'SİL ONAY';

        if ($confirmationText !== $requiredText) {
            return response()->json([
                'mesaj' => 'Doğrulama metni yanlış.'
            ], 422);
        }

        try {
            $order = Order::findOrFail($orderId);
            $order->delete();
            return response()->json([
                'mesaj' => 'Sipariş başarıyla silindi.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'mesaj' => 'Sipariş silinirken hata oluştu.'
            ], 500);
        }
    }

    // Teslimat adresinin güncellenmesi
    public function updateShippingAddress(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:200',
            'address'   => 'required|string|max:500',
            'city'      => 'required|string|max:100',
            'district'  => 'required|string|max:100',
            'country'   => 'required|string|max:100',
            'phone'     => 'required|string|max:20',
        ], [
            'full_name.required' => 'Alıcı adı gereklidir.',
            'address.required'   => 'Adres gereklidir.',
            'city.required'      => 'Şehir gereklidir.',
            'district.required'  => 'İlçe gereklidir.',
            'country.required'   => 'Ülke gereklidir.',
            'phone.required'     => 'Telefon numarası gereklidir.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mesaj'   => 'Teslimat adresi doğrulama hataları.',
                'hatalar' => $validator->errors()
            ], 422);
        }

        try {
            $order = Order::findOrFail($orderId);
            if ($order->shippingAddress) {
                $order->shippingAddress()->update($validator->validated());
            } else {
                $order->shippingAddress()->create($validator->validated());
            }
            return response()->json([
                'mesaj' => 'Teslimat adresi güncellendi.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'mesaj' => 'Teslimat adresi güncellenirken hata oluştu.'
            ], 500);
        }
    }

    // Fatura bilgilerinin güncellenmesi
    public function updateInvoiceInfo(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'invoice_type' => 'required|in:C,I',
            'company_name' => 'required_if:invoice_type,I|max:255',
            'name'         => 'required_if:invoice_type,C|max:255',
            'surname'      => 'required_if:invoice_type,C|max:255',
            'tc_number'    => 'required_if:invoice_type,C|digits:11',
            'address'      => 'required|string|max:500',
            'tax_office'   => 'required_if:invoice_type,I|max:255',
            'tax_number'   => 'required_if:invoice_type,I|max:50',
            'email'        => 'nullable|email|max:255',
        ], [
            'invoice_type.required'       => 'Fatura tipi gereklidir.',
            'invoice_type.in'             => 'Fatura tipi yalnızca C veya I olabilir.',
            'company_name.required_if'    => 'Kurumsal fatura için şirket adı gereklidir.',
            'name.required_if'            => 'Bireysel fatura için ad gereklidir.',
            'surname.required_if'         => 'Bireysel fatura için soyad gereklidir.',
            'tc_number.required_if'       => 'Bireysel fatura için TC kimlik numarası gereklidir.',
            'tc_number.digits'            => 'TC kimlik numarası 11 haneli olmalıdır.',
            'address.required'            => 'Adres gereklidir.',
            'tax_office.required_if'      => 'Kurumsal fatura için vergi dairesi gereklidir.',
            'tax_number.required_if'      => 'Kurumsal fatura için vergi numarası gereklidir.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mesaj'   => 'Fatura bilgileri doğrulama hataları.',
                'hatalar' => $validator->errors()
            ], 422);
        }

        try {
            $order = Order::findOrFail($orderId);
            if ($order->invoiceInfo) {
                $order->invoiceInfo()->update($validator->validated());
            } else {
                $order->invoiceInfo()->create($validator->validated());
            }
            return response()->json([
                'mesaj' => 'Fatura bilgileri güncellendi.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'mesaj' => 'Fatura bilgileri güncellenirken hata oluştu.'
            ], 500);
        }
    }

    // Müşteri bilgilerinin güncellenmesi
    public function updateCustomerInfo(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:255',
        ], [
            'name.required'  => 'Müşteri adı gereklidir.',
            'phone.required' => 'Telefon numarası gereklidir.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mesaj'   => 'Müşteri bilgileri doğrulama hataları.',
                'hatalar' => $validator->errors()
            ], 422);
        }

        try {
            $order = Order::findOrFail($orderId);
            if ($order->customerInfo) {
                $order->customerInfo()->update($validator->validated());
            } else {
                $order->customerInfo()->create($validator->validated());
            }
            return response()->json([
                'mesaj' => 'Müşteri bilgileri güncellendi.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'mesaj' => 'Müşteri bilgileri güncellenirken hata oluştu.'
            ], 500);
        }
    }
}
