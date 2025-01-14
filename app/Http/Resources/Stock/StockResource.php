<?php

namespace App\Http\Resources\Stock;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
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
            'product_type' => $this->productType->product_type ?? null,
            'color_name' => $this->color->color_name ?? null,
            'color_hex' => $this->color->color_hex ?? null,
            'quantity' => $this->quantity,
        ];
    }
}
