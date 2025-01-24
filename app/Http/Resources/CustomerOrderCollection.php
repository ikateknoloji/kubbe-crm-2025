<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerOrderCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Toplam değerler
        $totalPrice = $this->collection->sum('total_price');
        $totalAmount = $this->collection->sum('total_amount');

        // Ortalama birim fiyat
        $averageUnitPrice = $totalAmount > 0 ? $totalPrice / $totalAmount : 0;

        return [
            'totals' => [
                'total_price' => round($totalPrice, 2),
                'total_amount' => $totalAmount,
                'average_unit_price' => round($averageUnitPrice, 2),
            ],
            'data' => $this->collection,
        ];
    }
}
