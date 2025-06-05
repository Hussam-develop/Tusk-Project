<?php

namespace App\Http\Requests;

use app\Traits\handleResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DoctorTimeRequest extends FormRequest
{
    use handleResponseTrait;
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
            //DON'T Need these Current
            // 'day' => ['required', 'string', 'max:255'],
            // 'start_time' => ['required', 'string', 'max:255'],
            // 'end_time' => ['required', 'string', 'max:15', 'unique:secretaries,phone'],
            // 'start_rest' => ['required', 'string', 'max:255', 'unique:secretaries,email'],
            // 'end_rest' => ['required', 'string', 'max:255'],

        ];
    }
    public function messages()
    {
        return [
            // 'first_name.required' => 'حقل الاسم الأول مطلوب.',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = $this->returnErrorMessage($errors->messages(), 422);

        throw new HttpResponseException($response);
    }
}
