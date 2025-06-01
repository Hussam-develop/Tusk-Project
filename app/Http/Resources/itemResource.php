<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class itemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
            'name' => $this->name,
            'quantity' => $this->quantity,
            'standard_quantity' => $this->standard_quantity,
            'minimum_quantity' => $this->minimum_quantity,
            'unit' => $this->unit,
            'is_static' => $this->is_static,
        ];
    }
}
