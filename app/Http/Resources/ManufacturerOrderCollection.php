<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ManufacturerOrderCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => ManufacturerOrderResource::collection($this->collection),
            'totals' => [
                'total_amount' => $this->collection->sum('total_amount'),
            ],
        ];
    }
}
