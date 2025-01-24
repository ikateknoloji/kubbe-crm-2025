<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $vatRate = 0.20; // %20 KDV oranı
        $priceWithoutVAT = $this->total_price / (1 + $vatRate); // KDV hariç fiyat hesaplama

        return [
            'id' => $this->id,
            'average_unit_price' => $this->average_unit_price,
            'total_price' => $this->total_price,
            'price_without_vat' => round($priceWithoutVAT, 2), // KDV hariç fiyat
            'total_amount' => $this->total_amount,
            'created_at' => $this->created_at->toDateTimeString(),
            'order' => $this->whenLoaded('order', function () {
                return [
                    'order_name' => $this->order->order_name,
                    'order_code' => $this->order->order_code,
                ];
            }),
        ];
    }
}
