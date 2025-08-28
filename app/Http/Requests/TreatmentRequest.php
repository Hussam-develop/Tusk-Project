<?php

namespace App\Http\Requests;

use app\Traits\handleResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TreatmentRequest extends FormRequest
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
            'patient_id'      => ['required', "integer", 'exists:patients,id'],
            'cost'      => ['required', "integer", "gt:0"],
            'type'      => ['required', 'string'],
            'details'      => ['string'],
            'date'      => ['required', 'date', 'after:2025-05-05'],
            'treatment_screenshot'      => ['file']
            // 'images'      => ['file'],

        ];
        return $rules;
    }
    public function messages()
    {
        return [

            'patient_id.required'    =>  "يجب إدخال رقم تعريف المريض patient_id",
            'patient_id.exists'      =>  "المريض غير موجود مسبقاً",

            'cost.required'      => "يجب إدخال تكلفة الجلسة",
            'cost.integer'      => "يجب إدخال تكلفة الجلسة كرقم",
            'cost.gt'      => "يجب إدخال تكلفة الجلسة كرقم أكبر من الصفر",

            'type..required'      => "يجب إدخال نوع الجلسة العلاجية",
            // 'type.string'      => "",

            // 'details.string'      => "",

            'date.required'      => "يجب إدخال تاريخ الجلسة العلاجية",
            'date.date'      => "يجب إدخال تاريخ الجلسة العلاجية بصيغة تاريخ",
            'date.after'      => " يجب إدخال تاريخ الجلسة العلاجية بعد تاريخ 2025-05-05  ",

            'case_screenshot.file'      => "يجب إدخال صور في هذا الحقل (case_screenshot)",
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = $this->returnErrorMessage($errors->messages(), 422);

        throw new HttpResponseException($response);
    }
}
