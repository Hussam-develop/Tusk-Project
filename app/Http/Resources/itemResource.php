<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
            'id' => $this->id,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'standard_quantity' => $this->standard_quantity,
            'minimum_quantity' => $this->minimum_quantity,
            'is_static' => $this->is_static,
            'unit' => $this->unit,
            'item_history' => $this->itemHistory->map(function ($history) {
                return [
                    'item_id' => $history->item_id,
                    'unit_price' => $history->unit_price,
                    'total_price' => $history->total_price,
                    'quantity' => $history->quantity,
                    'created_at' => $history->created_at,
                    'updated_at' => $history->updated_at,
                ];
            }),
        ];
    }
}
