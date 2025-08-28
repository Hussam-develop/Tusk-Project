<?php

namespace App\Http\Requests;

use app\Traits\handleResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RenewSubscriptionClinicRequest extends FormRequest
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
            'dentist_id' => 'required|exists:dentists,id',
            'months' => 'required|integer|min:1',
            'subscription_value' => 'required|numeric|min:0'
        ];
    }

    public function messages()
    {
        return [
            'dentist_id.required' => 'يجب تقديم معرف العيادة.',
            'dentist_id.exists' => 'معرف العيادة المدخل غير موجود.',
            'months.required' => 'يجب تقديم عدد الشهور.',
            'months.integer' => 'يجب أن يكون عدد الشهور عدد صحيح.',
            'months.min' => 'يجب أن يكون عدد الشهور على الأقل 1.',
            'subscription_value.required' => 'يجب تقديم قيمة الاشتراك.',
            'subscription_value.numeric' => 'يجب أن تكون قيمة الاشتراك رقم.',
            'subscription_value.min' => 'يجب أن تكون قيمة الاشتراك أكبر من أو تساوي 0.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = $this->returnErrorMessage($errors->messages(), 422);

        throw new HttpResponseException($response);
    }
}
