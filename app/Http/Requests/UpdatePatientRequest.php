<?php

namespace App\Http\Requests;

use app\Traits\handleResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePatientRequest extends FormRequest
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
            'full_name'       => ['nullable', 'string', 'min:7', 'max:30'],
            'phone'            => ['nullable', 'string', 'max:12', 'regex:/^[0-9+\-\s]{7,20}$/'],
            'birthday'         => ['nullable', 'date', 'before:today'],
            'is_smoker'        => ['nullable', 'boolean'],
            'address'          => ['nullable', 'string', 'max:100'],
            'gender'           => ['nullable', 'in:ذكر,أنثى'],
            // 'current_balance'  =>['nullable'],
            'medicine_name'    => ['nullable', 'string', 'max:100'],
            'illness_name'     => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.min'      => 'الاسم  يجب ان يكون اكبر من 7 احرف.',
            'phone.regex'              => 'يرجى إدخال رقم هاتف صحيح.',
            'birthday.date'            => 'يجب أن يكون تاريخ الميلاد تاريخًا صالحًا.',
            'birthday.before'          => 'تاريخ الميلاد يجب أن يكون في الماضي.',
            'is_smoker.boolean'        => 'القيمة يجب أن تكون 0 أو 1.',
            'gender.in'                => 'الجنس يجب أن يكون: male أو female أو .',
            'medicine_name.string'     => 'اسم الدواء يجب أن يكون نصًا.',
            'medicine_name.max'      => 'اسم الدواء يجب أن يكون اقل من 100 حرف.',
            'illness_name.string'      => 'اسم المرض يجب أن يكون نصًا.',
            'illness_name.max'      => 'اسم المرض يجب أن يكون اقل من 100 حرف.',
            'address.string'      => ' العنوان يجب أن يكون نصًا.',
            'address.max'      => 'العنوان  يجب أن يكون اقل من 100 حرف.',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = $this->returnErrorMessage($errors->messages(), 422);

        throw new HttpResponseException($response);
    }
}
