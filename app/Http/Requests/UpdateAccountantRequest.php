<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountantRequest extends FormRequest
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
            'full_name' => ['sometimes', 'string', 'min:3', 'max:50'],
            'email' => ['sometimes', 'email', 'unique:accountants,email,' . $id],
            'work_start_at' => ['sometimes'],
            'phone' => [
                'sometimes',
                'string',
                'size:10',
                'regex:/^[0-9]+$/',
                'starts_with:09',
                'unique:accountants,phone,' . $id,
            ],

        ];
    }


    public function messages(): array
    {
        return [
            'full_name.required' => 'الاسم  مطلوب.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'يرجى إدخال عنوان بريد إلكتروني صالح.',
            'email.unique' => 'هذا البريد الإلكتروني مسجّل بالفعل.',
            'phone.size' => ' يجب أن يكون رقم العيادة مكوّن من 10 أرقام حصراً ',
            'phone.regix' => 'يجب أن يكون الرقم مكون من أرقام فقط',
            'phone.starts_with' => ' يجب أن يبدأ الرقم بـ 09 حصراً ',
            'phone.unique' => 'هذا رقم  مستخدم سابقاً. يجب إدخال رقم آخر',
            'work_start_at.required' => 'تاريخ بدء العمل مطلوب',


        ];
    }
}
