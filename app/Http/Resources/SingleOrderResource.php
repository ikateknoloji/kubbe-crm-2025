<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order' => new OrderDefaultResource($this),

            'customer' => $this->whenLoaded('customer', function () {
                return new CustomerResource($this->customer);
            }),
            'customer_info' => $this->whenLoaded('customerInfo', function () {
                return new CustomerInfoResource($this->customerInfo);
            }),
            'manufacturer' => $this->whenLoaded('manufacturer', function () {
                return new ManufacturerResource($this->manufacturer);
            }),

            'payment_receipt' => $this->whenLoaded('paymentReceipt', function () {
                return new PaymentReceiptResource($this->paymentReceipt);
            }),

            'shipping_address' => $this->whenLoaded('shippingAddress', function () {
                return new ShippingAddressResource($this->shippingAddress);
            }),

            'order_images' => $this->whenLoaded('orderImages', function () {
                return OrderImageResource::collection($this->orderImages);
            }),

            'invoice_info' => $this->whenLoaded('invoiceInfo', function () {
                return new InvoiceInfoResource($this->invoiceInfo);
            }),

            'order_baskets' => $this->whenLoaded('orderBaskets', function () {
                return new OrderBasketCollection($this->orderBaskets);
            }),
            'order_timeline' => $this->whenLoaded('timeline', function () {
                return new OrderTimelineResource($this->timeline);
            }),
        ];
    }
}
