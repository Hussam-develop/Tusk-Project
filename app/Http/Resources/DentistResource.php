<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DentistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id'         => $this->id,
            'name' => $this->first_name . '' . $this->last_name,
            'phone'      => $this->phone,
            'address'    => $this->address,

        ];

        $accepted = optional($this->pivot)->request_is_accepted;

        if ($accepted) {
            $data['joined_on'] = optional($this->pivot)->updated_at?->toDateTimeString();
            $data['current_account'] = $this->latestAccountRecord?->current_account ?? 0;
        } else {
            $data['request_date'] = optional($this->pivot)->created_at?->toDateTimeString();
        }


        return $data;
    }
}
