<?php

namespace App\Http\Requests;

use app\Traits\HandleResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangeCaseStatusRequest extends FormRequest
{
    use HandleResponseTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;

        //return in_array($this->input('guard'), ['secretary', 'inventory_employee', 'accountant']);
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
            'case_id' => ['required', 'exists:medical_cases,id'],
            // 'new_status' => ['required', "integer", 'between:1,3'/*'string', 'in:accepted,in progress,ready' */], //pending and delivered status is denied
            'cost' => ['required', "integer"]
        ];
        return $rules;
    }
    public function messages()
    {
        return [

            'cost.required' => 'يجب إدخال التكلفة',
            // 'new_status.between' => 'يجب إدخال رقم نوع الحالة بحيث يكون ضمن القيم 1 أو 2 أو 3 فقط',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = $this->returnErrorMessage($errors->messages(), 422);

        throw new HttpResponseException($response);
    }
}
