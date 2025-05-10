<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabResource extends JsonResource
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
            'lab_name' => $this->lab_name,
            'lab_phone' => json_decode($this->lab_phone, true),
            'lab_address' => $this->lab_address,
            'register_date' => $this->register_date,
        ];

        // return ['message' => 'لا يوجد مخابر مشتركة بالمنصة.'];



        // // return  [
        // //     "id"=> $this->id,
        // //     'lab_name' => $this->lab_name,
        // //     'lab_phone' => $this->lab_phone,
        // //     'lab_address' => $this->lab_address,
        // //     'register_date' => $this->register_date,

        // // ];
    }
}
