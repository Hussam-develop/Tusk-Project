<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $data = [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'start_at' => $this->work_start_at

        ];
        if ($this->active) {
            $data['id'] = $this->id;
            $data['email'] = $this->email;
            $data['phone'] = $this->phone;
        } else {
            $data['termination_at'] = $this->updated_at->toDateTimeString();
        }

        return $data;
    }
}
