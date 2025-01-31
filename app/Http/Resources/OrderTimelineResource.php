<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderTimelineResource extends JsonResource
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
            'approved_at' => $this->approved_at?->format('H:i - d/m/Y'),
            'production_started_at' => $this->production_started_at?->format('H:i - d/m/Y'),
            'production_completed_at' => $this->production_completed_at?->format('H:i - d/m/Y'),
            'shipped_at' => $this->shipped_at?->format('H:i - d/m/Y'),
        ];
    }
}
