<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EditBasketCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($basket) {
                return [
                    ...(new OrderItemResource($basket->orderItem))->toArray(request()),
                    'order_logos' => $basket->orderLogos->toArray(),
                ];
            }),
        ];
    }
}
