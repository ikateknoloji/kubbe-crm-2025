<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManufacturerOrderResource extends JsonResource
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
            'order_id' => $this->order_id,
            'total_amount' => $this->total_amount,
            'manufacturer_id' => $this->manufacturer_id,
            'order' => [
                'order_name' => $this->order->order_name ?? null,
                'order_code' => $this->order->order_code ?? null,
                'status' => $this->order->status ?? null,
            ],
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
