<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class registerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array($this->input('guard'), ['dentist', 'lab_manager']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $guard = $this->input('guard');

        $commonRules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:' . $this->getTableName($guard) . ',email'],
            'password'   => ['required', 'string', 'min:6'],
            'guard'      => ['required', 'in:dentist,lab_manager'],

        ];

        $labManagerRules = [
            'lab_name'      => ['required', 'string'],
            'lab_type'      => ['required', 'string'],
            'lab_from_hour' => ['required'],
            'lab_to_hour'   => ['required'],
            'lab_phone'     => ['required'],
            'lab_province'  => ['required', 'string'],
            'lab_address'   => ['required', 'string'],

        ];
        $dentistRules = [
            'phone'      => ['required', 'string', 'max:20'],
            'address'    => ['required', 'string'],
        ];

        return match ($guard) {
            'dentist'     => array_merge($commonRules, $dentistRules),
            'lab_manager' => array_merge($commonRules, $labManagerRules),
        };
    }


    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required'  => 'Last name is required.',
            'email.required'      => 'Email is required.',
            'email.email'         => 'Please enter a valid email address.',
            'email.unique'        => 'This email is already registered.',
            'password.required'   => 'Password is required.',
            'password.min'        => 'Password must be at least 6 characters.',
            'password.confirmed'  => 'Password confirmation does not match.',
            'phone.required'      => 'Phone number is required.',
            'guard.required'      => 'User type is required.',
            'guard.in'            => 'User type must be either dentist or lab manager.',

            'image_path.required'           => 'Dentist profile image is required.',
            'clinic_name.required'          => 'Clinic name is required.',
            'clinic_province.required'      => 'Clinic province is required.',
            'address.required'       => 'Clinic address is required.',
            'clinic_phone.required'         => 'Clinic phone is required.',
            'register_date.required' => 'Clinic register date is required.',
            'register_date.date'     => 'Clinic register date must be a valid date.',

            'lab_logo.required'      => 'Lab logo is required.',
            'lab_name.required'      => 'Lab name is required.',
            'lab_province.required'  => 'Lab province is required.',
            'lab_address.required'   => 'Lab address is required.',
            'lab_phone.required'     => 'Lab phone is required.',
            'lab_type.required'      => 'Lab type is required.',
            'lab_from_hour.required' => 'Lab opening hour is required.',
            'lab_to_hour.required'   => 'Lab closing hour is required.',
        ];
    }

    private function getTableName(string $guard): string
    {
        return match ($guard) {
            'dentist'     => 'dentists',
            'lab_manager' => 'lab_managers',
        };
    }
}
