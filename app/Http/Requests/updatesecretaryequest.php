<?php

namespace App\Http\Requests;

use app\Traits\HandleResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class Updatesecretaryequest extends FormRequest
{
    use HandleResponseTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:15', 'unique:secretaries,phone'],
            'email' => ['required', 'string', 'max:255', 'unique:secretaries,email'],
            'attendance_time' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255']

        ];
    }
    public function messages()
    {
        return [
            'first_name.required' => 'حقل الاسم الأول مطلوب.',
            'first_name.string' => 'حقل الاسم الأول يجب أن يكون نصًا.',
            'first_name.max' => 'حقل الاسم الأول يجب أن لا يتجاوز 255 حرفًا.',

            'last_name.required' => 'حقل الاسم الأخير مطلوب.',
            'last_name.string' => 'حقل الاسم الأخير يجب أن يكون نصًا.',
            'last_name.max' => 'حقل الاسم الأخير يجب أن لا يتجاوز 255 حرفًا.',

            'phone.required' => 'حقل رقم الهاتف مطلوب.',
            'phone.string' => 'حقل رقم الهاتف يجب أن يكون نصًا.',
            'phone.max' => 'حقل رقم الهاتف يجب أن لا يتجاوز 15 حرفًا.',
            'phone.unique' => 'حقل رقم الهاتف مكرر ادخل غيره .',

            'email.required' => 'حقل البريد الإلكتروني مطلوب.',
            'email.email' => 'البريد الإلكتروني غير صحيح.',
            'email.max' => 'حقل البريد الإلكتروني يجب أن لا يتجاوز 255 حرفًا.',
            'email.unique' => 'حقل البريد الإلكتروني مكرر ادخل غيره .',

            'attendance_time.required' => 'حقل وقت الحضور مطلوب.',
            'attendance_time.string' => 'حقل وقت الحضور يجب أن يكون نصًا.',
            'attendance_time.max' => 'حقل وقت الحضور يجب أن لا يتجاوز 255 حرفًا.',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = $this->returnErrorMessage($errors->messages(), 422);

        throw new HttpResponseException($response);
    }
}
