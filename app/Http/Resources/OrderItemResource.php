<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'order_basket_id'  => $this->order_basket_id,
            'stock_id'         => $this->stock_id,
            'quantity'         => $this->quantity,
            'unit_price'       => $this->unit_price,
            'color_name'       => $this->stock?->color?->color_name,
            'color_hex'        => $this->stock?->color?->color_hex,
            'product_type'     => $this->stock?->productType?->product_type,
        ];
    }
}
