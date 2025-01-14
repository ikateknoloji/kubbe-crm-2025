<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'invoice_type' => $this->invoice_type,
            'company_name' => $this->company_name,
            'name'         => $this->name,
            'surname'      => $this->surname,
            'tc_number'    => $this->tc_number,
            'address'      => $this->address,
            'tax_office'   => $this->tax_office,
            'tax_number'   => $this->tax_number,
            'email'        => $this->email,
        ];
    }
}
