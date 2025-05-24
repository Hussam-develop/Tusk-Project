<?php

namespace App\Http\Requests;

use app\Traits\handleResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class addCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'name' => ['required', 'string', 'max:255'],


        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'حقل الاسم  مطلوب.',

        ];
    }
     protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = $this->returnErrorMessage($errors->messages(), 422);

        throw new HttpResponseException($response);
    }
}
