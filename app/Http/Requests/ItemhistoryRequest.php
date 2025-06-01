<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use app\Traits\handleResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ItemhistoryRequest extends FormRequest
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
    public function rules()
    {
        return [
            'quantity' => 'required|numeric|not_in:0',
            'unit_price' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'quantity.required' => 'الكمية مطلوبة.',
            'quantity.numeric' => 'الكمية يجب أن تكون رقمًا.',
            'quantity.not_in' => 'لا يمكن أن تكون الكمية مساوية للصفر.',
            'unit_price.required' => 'سعر الوحدة مطلوب.',
            'unit_price.numeric' => 'سعر الوحدة يجب أن يكون رقمًا.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = $this->returnErrorMessage($errors->messages(), 422);

        throw new HttpResponseException($response);
    }
}
