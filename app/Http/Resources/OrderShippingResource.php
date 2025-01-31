<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderShippingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order_id' => $this->order_id,
            'tracking_code' => $this->tracking_code,
            'shipping_company' => $this->shipping_company,
            'created_at' => $this->created_at?->format('H:i - d/m/Y'),
            'updated_at' => $this->updated_at?->format('H:i - d/m/Y'),
        ];
    }
}
