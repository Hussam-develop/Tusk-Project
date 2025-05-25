<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreatmentResource extends JsonResource
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
            'type' => $this->type,
            'date' => $this->date,

            // Relations Examples :
            // 'year' =>
            // YearsResource::collection($this->whenLoaded('years')),

        ];
    }
}
