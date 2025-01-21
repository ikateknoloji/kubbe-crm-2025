<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\OrderStatus;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order' => [
                'id' => $this->id,
                'order_name' => $this->order_name,
                'order_code' => $this->order_code,
                'status' => $this->status,
                'status_label' => $this->getStatusLabel(), 
                'paid_amount' => $this->paid_amount,
                'offer_price' => $this->offer_price,
                'created_at' => $this->created_at,
            ],
            'customer' => new CustomerResource(
                $this->whenLoaded('customer')
            ),
            'manufacturer' => new ManufacturerResource(
                $this->whenLoaded('manufacturer')
            ),
            'all_logo' => $this->orderLogos->pluck('image'),
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
