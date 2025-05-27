<?php

namespace App\Http\Requests;

use app\Traits\handleResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PatientPaymentRequest extends FormRequest
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
            'patient_id'      => ['required', 'int', "exists:patients,id"],
            'value'      => ['required', "integer", "gt:0"],
            'payment_date'      => ['required', 'date'],
        ];
        return $rules;
    }
    public function messages()
    {
        return [
            'patient_id.required' => 'رقم تعريف المريض مطلوب.',
            'patient_id.int' => 'يجب أن يكون رقم تعريف المريض رقمًا صحيحًا.',
            'patient_id.exists' => 'المريض غير موجود في قاعدة البيانات.',

            'value.required' => 'القيمة المدفوعة مطلوبة.',
            'value.integer' => 'يجب أن تكون القيمة المدفوعة رقمًا صحيحًا.',
            'value.gt' => 'يجب أن تكون القيمة المدفوعة أكبر من 0.',

            'payment_date.required' => 'تاريخ الدفع مطلوب.',
            'payment_date.date' => 'يجب أن يكون تاريخ الدفع تاريخًا صحيحًا.',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = $this->returnErrorMessage($errors->messages(), 422);

        throw new HttpResponseException($response);
    }
}
