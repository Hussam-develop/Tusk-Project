<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
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
            'full_name'       => ['required', 'string', 'max:30'],
            'phone'            => ['required', 'string', 'regex:/^[0-9+\-\s]{7,20}$/'],
            'birthday'         => ['required', 'date', 'before:today'],
            'is_smoker'        => ['required', 'boolean'],
            'address'          => ['required', 'string', 'max:100'],
            'gender'           => ['required', 'in:ذكر,انثى'],
            'current_balance'  => ['nullable', 'numeric'],
            'medicine_name'    => ['nullable', 'string', 'max:100'],
            'illness_name'     => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required'      => 'الاسم الكامل مطلوب.',
            'phone.required'           => 'رقم الهاتف مطلوب.',
            'phone.regex'              => 'يرجى إدخال رقم هاتف صحيح.',
            'birthday.required'        => 'تاريخ الميلاد مطلوب.',
            'birthday.date'            => 'يجب أن يكون تاريخ الميلاد تاريخًا صالحًا.',
            'birthday.before'          => 'تاريخ الميلاد يجب أن يكون في الماضي.',
            'is_smoker.required'       => 'يرجى تحديد ما إذا كان المريض مدخنًا.',
            'is_smoker.boolean'        => 'القيمة يجب أن تكون 0 أو 1.',
            'gender.required'          => 'الجنس مطلوب.',
            'gender.in'                => 'الجنس يجب أن يكون: male أو female أو .',
            'medicine_name.string'     => 'اسم الدواء يجب أن يكون نصًا.',
            'medicine_name.max'      => 'اسم الدواء يجب أن يكون اقل من 100 حرف.',
            'illness_name.string'      => 'اسم المرض يجب أن يكون نصًا.',
            'illness_name.max'      => 'اسم المرض يجب أن يكون اقل من 100 حرف.',
            'address.string'      => ' العنوان يجب أن يكون نصًا.',
            'address.max'      => 'العنوان  يجب أن يكون اقل من 100 حرف.',
        ];
    }
}
