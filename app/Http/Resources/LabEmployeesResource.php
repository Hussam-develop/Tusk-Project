<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabEmployeesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'active_inventory_employee' => new InventoryEmployeeResource($this['active_inventory_employee']),
            'inactive_inventory_employees' => InventoryEmployeeResource::collection($this['inactive_inventory_employees']),

            'active_accountant' => new AccountantResource($this['active_accountant']),
            'inactive_accountants' => AccountantResource::collection($this['inactive_accountants']),
        ];
    }
}
