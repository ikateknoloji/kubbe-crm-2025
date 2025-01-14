<?php

namespace App\Http\Resources\Bill;

use App\Http\Resources\InvoiceInfoResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoicedOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_name' => $this->order_name,
            'order_code' => $this->order_code,
            'status' => $this->status,
            'note' => $this->note,
            'paid_amount' => $this->paid_amount,
            'offer_price' => $this->offer_price,
            'created_at' => $this->created_at,
            'order_items' => $this->whenLoaded('orderBaskets', function () {
                return OrderItemResource::collection(
                    $this->orderBaskets->pluck('orderItem')->filter()->values()
                );
            }),
            'totals' => $this->whenLoaded('orderBaskets', function () {
                $orderItems = $this->orderBaskets->pluck('orderItem')->filter()->values();
                $total_price = $orderItems->sum(fn($item) => $item->unit_price * $item->quantity);
                $total_price_without_vat = $total_price / 1.20;

                return [
                    'total_price' => number_format($total_price, 2, '.', ''), 
                    'total_price_without_vat' => number_format($total_price_without_vat, 2, '.', '')
                ];
            }),
            'invoice_info' => $this->whenLoaded('invoiceInfo', function () {
                return new InvoiceInfoResource($this->invoiceInfo);
            }),
        ];
    }
}
