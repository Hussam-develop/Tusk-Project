<?php

namespace App\Http\Requests\Mails;

use app\Traits\HandleResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ForgetPasswordRequest extends FormRequest
{
    use HandleResponseTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array($this->input('guard'), ['dentist', 'lab_manager', 'secretary', 'inventory_employee', 'accountant', 'admin']);
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
            'guard'      => ['required', 'in: dentist,lab_manager,secretary,inventory_employee,accountant,admin'],
            'new_password' => [
                'required',
                'min:6',
                'confirmed',
                // Password::defaults() /*be sure that minimum size of the password is 6 not 8 in Illuminate\Validation\Rules\Password  */
            ],
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
            'guard.in'            => 'نوع المستخدم يجب أن يكون dentist أو lab_manager.',

            'new_password.required' => ' لم يتم إدخال كلمة السر الجديدة',
            'new_password.min' => 'يجب أن تكون كلمة السر الجديدة مكونة من 6 رموز على الأقل',
            'new_password.confirmed' => 'تأكيد كلمة السر غير مطابق',
        ];
    }
    private function getTableName(string $guard): string
    {
        return match ($guard) {
            'admin'     => 'admins',
            'lab_manager' => 'lab_managers',
            'dentist'     => 'dentists',
            'secretary'     => 'secretaries',
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
