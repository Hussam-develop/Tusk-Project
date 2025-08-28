<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use app\Traits\handleResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class AddEmployeeRequest extends FormRequest
{

    use handleResponseTrait;
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
        $guard = $this->input('guard');

        return [
            'full_name' => ['required', 'string', 'min:3', 'max:50'],
            'email' => ['required', 'email', 'unique:' . $this->getTableName($guard) . ',email'],
            'work_start_at' => ['required'],
            'phone' => [
                'required',
                'string',
                'size:10',
                'regex:/^[0-9]+$/',
                'starts_with:09',
                'unique:' . $this->getTableName($guard) . ',phone'
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

    private function getTableName(string $guard): string
    {
        return match ($guard) {
            'accountant' => 'accountants',
            'inventory_employee' => 'inventory_employees',
        };
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = $this->returnErrorMessage($errors->messages(), 422);

        throw new HttpResponseException($response);
    }
}
