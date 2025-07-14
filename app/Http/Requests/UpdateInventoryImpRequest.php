<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryImpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'full_name' => ['sometimes', 'string', 'min:3', 'max:15'],
            'email' => ['sometimes', 'email', 'unique:inventory_employees,email,' . $id],
            'work_start_at' => ['sometimes', 'required'],
            'phone' => [
                'sometimes',
                'string',
                'size:10',
                'regex:/^[0-9]+$/',
                'starts_with:09',
                'unique:inventory_employees,phone,' . $id,
            ],


        ];
    }
}
