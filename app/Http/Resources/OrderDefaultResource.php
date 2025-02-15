<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\OrderStatus;

class OrderDefaultResource extends JsonResource
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
            'is_rejected' => $this->is_rejected,
            'note' => $this->note,
            'shipping_type' => $this->shipping_type,
            'invoice_status' => $this->invoice_status,
            'paid_amount' => $this->paid_amount,
            'offer_price' => $this->offer_price,
            'customer_id' => $this->customer_id,
            'manufacturer_id' => $this->manufacturer_id,
            'status_label' => $this->getStatusLabel(), 
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    
    private function getStatusLabel(): string
    {
        if ($this->status instanceof OrderStatus) {
            return $this->status->label();
        }
    
        $statusEnum = OrderStatus::tryFrom($this->status);
        return $statusEnum ? $statusEnum->label() : 'Bilinmeyen Durum';
    }
}
