<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class latestAccountInLabResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
            // 'dentist_id'=>$this->dentist_id,
            // 'lab_manager_id'=>$this->lab_manager_id,
            // 'bill_id'=>$this->bill_id,
            // 'creatorable_id'=>$this->creatorable_id,
            // 'creatorable_type'=>$this->creatorable_type,
            // 'note'=>$this->note,
            // 'type'=>$this->type,
            // 'signed_value'=>$this->signed_value,
            "id" => $this->id,
            'current_account' => $this->current_account,
        ];
    }
}
