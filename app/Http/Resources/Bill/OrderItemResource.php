<?php

namespace App\Http\Resources\Bill;

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
        $kdv_orani = 0.20;
        $unit_price = (float) $this->unit_price; 
        $unit_price_without_vat = $unit_price / (1 + $kdv_orani); 

        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'unit_price_without_vat' => $unit_price_without_vat,
            'product_type' => optional($this->stock->productType)->product_type,
            'color_name' => optional($this->stock->color)->color_name,
            'color_hex' => optional($this->stock->color)->color_hex,
        ];
    }
}
