<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use app\Traits\handleResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    use handleResponseTrait;
    /*
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /*
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'email' => ['required', 'email', Rule::exists($this->getTableName($this->guard), 'email')],
            'password' => ['required', 'string', 'min:8'],
            'guard'    => ['required', 'in:dentist,lab_manager,accountant,inventory_employee,secretary,admin'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'البريد الإلكتروني مطلوب.',
            'email.email'       => 'يرجى إدخال بريد إلكتروني صحيح.',
            'email.exists'      => '  حاول اختيار نوع مستخدم اخر او قم بالتسجيل ضمن المنصة هذا البريد الإلكتروني غير موجود ضمن نوع المستخدم المحدد: ' . $this->getTableName($this->guard) . '.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min'      => 'يجب ألا تقل كلمة المرور عن 6 أحرف.',

            'guard.required'    => 'نوع المستخدم مطلوب.',
            'guard.in'          => 'أنواع المستخدمين المسموح بها: طبيب أسنان، مدير مخبر، محاسب، سكرتير، موظف مستودع.',

        ];
    }



    private function getTableName(string $guard): string
    {
        return match ($guard) {
            'admin' => 'admins',
            'dentist'     => 'dentists',
            'lab_manager' => 'lab_managers',
            'accountant' => 'accountants',
            'secretary'     => 'secretaries',
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
