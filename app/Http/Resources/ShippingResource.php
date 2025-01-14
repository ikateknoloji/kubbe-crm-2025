<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
            'id' => $this->id,
            'order_name' => $this->order_name,
            'order_code' => $this->order_code,
            'shipping_type' => $this->shipping_type,
            'status' => $this->status,
            'note' => $this->note,
            'shipping_address' => $this->whenLoaded('shippingAddress', function () {
                return [
                    'full_name' => $this->shippingAddress->full_name,
                    'address' => $this->shippingAddress->address,
                    'city' => $this->shippingAddress->city,
                    'district' => $this->shippingAddress->district,
                    'country' => $this->shippingAddress->country,
                    'phone' => $this->shippingAddress->phone,
                ];
            }),
            'order_images' => $this->whenLoaded('orderImages', function () {
                return $this->orderImages->pluck('image_path'); // Sadece görsel yolları döndür
            }),
        ];
    }
}
