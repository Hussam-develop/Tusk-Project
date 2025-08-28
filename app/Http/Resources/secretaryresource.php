<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Secretaryresource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
            "id" => $this->id,
            'full_name' => $this->full_name,
            // 'last_name' => $this->last_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'attendence_time' => $this->attendence_time,
            'address' => $this->address,
        ];
    }
}
