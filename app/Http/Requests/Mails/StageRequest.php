<?php

namespace App\Http\Requests\Mails;

use app\Traits\handleResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StageRequest extends FormRequest
{
    use handleResponseTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;

        //return in_array($this->input('guard'), ['secratary', 'inventory_employee', 'accountant']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $guard = $this->input('guard');
        $rules = [
            'email'      => [
                'required',
                'email',
                'exists:' . $this->getTableName($guard) . ',email'
            ],
            'guard'      => ['required', 'in:secratary,inventory_employee,accountant'],
            'verification_code' => ['required', 'integer'/*,"digits:6"*/]
        ];
        return $rules;
    }
    public function messages()
    {
        return [

            'email.required' => 'لم يتم إدخال الإيميل',
            'email.email' => 'الإيميل غير مكتوب بطريقة صحيحة',
            'email.exists' => 'هذا الإيميل غير مسجل مسبقاً. الرجاء إدخال إيميل آخر',

            'guard.required'      => 'نوع المستخدم مطلوب',
            'guard.in'            => " نوع المستخدم يجب أن يكون ضمن أحد القيم التالية : [secratary أو inventory_employee أو  accountant ]",

            'verification_code.required' => 'لم يتم إدخال رمز التحقق',
            'verification_code.integer' => 'يجب أن يكون رمز التحقق مكون من أرقام فقط',
        ];
    }
    private function getTableName(string $guard): string
    {
        return match ($guard) {
            'admin'     => 'admins',
            'lab_manager' => 'lab_managers',
            'dentist'     => 'dentists',
            'secratary'     => 'secretaries',
            'inventory_employee'   => 'inventory_employees',
            'accountant'     => 'accountants',
        };
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = $this->returnErrorMessage($errors->messages(), 422);

        throw new HttpResponseException($response);
    }
}
