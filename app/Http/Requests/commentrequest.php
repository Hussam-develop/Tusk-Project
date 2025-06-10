<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use app\Traits\handleResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class commentrequest extends FormRequest
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
        return [
            'comment' => 'required|string',

        ];
    }
    public function messages()
    {
        return [
            'comment.required' => ' التعليق مطلوب.',
            'comment.string' => 'يجب أن يكون التعليق نصًا.',

        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = $this->returnErrorMessage($errors->messages(), 422);

        throw new HttpResponseException($response);
    }
}
