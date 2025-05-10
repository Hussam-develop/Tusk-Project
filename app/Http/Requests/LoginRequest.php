<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
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

        return [
            'email'=> ['required','email',Rule::exists($this->getTableName($this->guard),'email')],
            'password' => ['required', 'string', 'min:6'],
            'guard'    => ['required', 'in:dentist,lab_manager,accountant'],
        ];
    }

    public function messages(): array
{
    return [
        'email.required'    => 'Email is required.',
        'email.email'       => 'Enter a valid email.',
        'email.exists'      => 'This email does not exist in the selected '.$this->getTableName($this->guard).'user type.',

        'password.required' => 'Password is required.',
        'password.min'      => 'Password must be at least 6 characters.',

        'guard.required'    => 'User type is required.',
        'guard.in'          => 'Allowed user types: dentist, lab_manager, accountant, secratary, inventory_employee',
    ];
}



    private function getTableName(string $guard): string
    {
        return match ($guard) {
            'dentist'     => 'dentists',
            'lab_manager' => 'lab_managers',
            'accountant'=>'accountants',
        };
    }
}
