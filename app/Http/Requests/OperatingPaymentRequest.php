<?php

namespace App\Http\Requests;

use app\Traits\handleResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OperatingPaymentRequest extends FormRequest
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
        $rules = [
            'name'      => ['required', 'string', "max:50", "min:3"],
            'value'      => ['required', "integer", "gt:0"]

        ];
        return $rules;
    }
    public function messages()
    {
        return [

            'value.required'      => "يجب إدخال المبلغ",
            'value.integer'      => "يجب إدخال المبلغ كرقم",
            'value.gt'      => "يجب إدخال المبلغ كرقم أكبر من الصفر",

            'name.required'      => "يجب إدخال اسم المصروف التشغيلي",
            // 'name.string'      => "",
            'name.max'      => "يجب إدخال 50 حرف على الأكثر",
            'name.min'      => "يجب إدخال 3 حرف على الأقل",
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = $this->returnErrorMessage($errors->messages(), 422);

        throw new HttpResponseException($response);
    }
}
