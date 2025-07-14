<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class storeDentistRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:dentists,phone',
            'address' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'الاسم الأول مطلوب.',
            'last_name.required' => 'اسم العائلة مطلوب.',
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.*.size' => ' يجب أن يكون الرقم مكوّن من 10 أرقام حصراً ',
            'phone.*.regix' => 'يجب أن يكون رقم العيادة مكون من أرقام فقط',
            'phone.*.starts_with' => ' يجب أن يبدأ رقم العيادة بـ 09 حصراً ',
            'phone.unique' => 'هذا الرقم مستخدم سابقاً. يجب إدخال رقم آخر',
            'address.required' => 'العنوان مطلوب.',
        ];
    }
}
