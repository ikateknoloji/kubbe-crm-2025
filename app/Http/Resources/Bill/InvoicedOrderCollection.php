<?php

namespace App\Http\Resources\Bill;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class InvoicedOrderCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => InvoicedOrderResource::collection($this->collection),
            'pagination' => $this->resource->toArray(), 
        ];
    }

    /**
     * Add pagination metadata.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'pagination' => [
                'current_page' => $this->resource->currentPage(),
                'total_pages' => $this->resource->lastPage(),
                'per_page' => $this->resource->perPage(),
                'total_items' => $this->resource->total(),
            ],
        ];
    }
}
