<?php

namespace App\Http\Requests;

use app\Traits\handleResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MedicalCaseRequest extends FormRequest
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
            'lab_manager_id'      => ['required', "integer", 'exists:lab_managers,id'],
            'treatment_id'      => ['required', "integer", 'exists:treatments,id'],
            'need_trial' => ['required', 'in:0,1'],
            'repeat' => ['required', 'in:0,1'],
            'shade' => ['required', 'string'],
            'expected_delivery_date' => ['date', "date_format:Y-m-d"],
            'notes' => ['string', "nullable"],

            'teeth_crown' => ['string', "nullable"],
            'teeth_pontic' => ['string', "nullable"],
            'teeth_implant' => ['string', "nullable"],
            'teeth_veneer' => ['string', "nullable"],
            'teeth_inlay' => ['string', "nullable"],
            'teeth_denture' => ['string', "nullable"],

            'bridges_crown' => ['string', "nullable"],
            'bridges_pontic' => ['string', "nullable"],
            'bridges_implant' => ['string', "nullable"],
            'bridges_veneer' => ['string', "nullable"],
            'bridges_inlay' => ['string', "nullable"],
            'bridges_denture' => ['string', "nullable"],

        ];
        return $rules;
    }
    public function messages()
    {
        return [
            'patient_id.required' => 'رقم تعريف المريض مطلوب.',
            'patient_id.integer' => 'رقم تعريف المريض يجب أن يكون عددًا صحيحًا.',
            'patient_id.exists' => 'رقم تعريف المريض غير موجود في قاعدة البيانات.',

            'lab_manager_id.required' => 'رقم تعريف مدير المختبر مطلوب.',
            'lab_manager_id.integer' => 'رقم تعريف مدير المختبر يجب أن يكون عددًا صحيحًا.',
            'lab_manager_id.exists' => 'رقم تعريف مدير المختبر غير موجود في قاعدة البيانات.',

            'treatment_id.required' => 'رقم تعريف الجلسة العلاجية مطلوب.',
            'treatment_id.integer' => 'رقم تعريف الجلسة العلاجية يجب أن يكون عددًا صحيحًا.',
            'treatment_id.exists' => 'رقم تعريف الجلسة العلاجية غير موجود في قاعدة البيانات.',

            'need_trial.required' => 'خيار يحتاج التجربة مطلوب.',
            'need_trial.in' => 'القيمة يجب أن تكون 0 أو 1 فقط.',

            'repeat.required' => 'خيار التكرار مطلوب.',
            'repeat.in' => 'القيمة يجب أن تكون 0 أو 1 فقط.',

            'shade.required' => 'الظل مطلوب.',
            'shade.string' => 'يجب أن يكون الظل نصًا.',

            'expected_delivery_date.date' => 'يجب أن يكون تاريخ التسليم المتوقع صالحًا.',
            'expected_delivery_date.date_format' => 'تنسيق التاريخ يجب أن يكون YYYY-MM-DD.',

            'notes.string' => 'يجب أن يكون الملاحظات نصًا.',

            'teeth_crown.string' => 'يجب أن يكون تاج الأسنان نصًا.',
            'teeth_pontic.string' => 'يجب أن يكون الجسر نصًا.',
            'teeth_implant.string' => 'يجب أن يكون زرع الأسنان نصًا.',
            'teeth_veneer.string' => 'يجب أن يكون قشرة الأسنان نصًا.',
            'teeth_inlay.string' => 'يجب أن يكون الحشو الداخلي للأسنان نصًا.',
            'teeth_denture.string' => 'يجب أن يكون طقم الأسنان نصًا.',

            'bridges_crown.string' => 'يجب أن يكون تاج الجسر نصًا.',
            'bridges_pontic.string' => 'يجب أن يكون الجسر نصًا.',
            'bridges_implant.string' => 'يجب أن يكون زرع الجسر نصًا.',
            'bridges_veneer.string' => 'يجب أن يكون قشرة الجسر نصًا.',
            'bridges_inlay.string' => 'يجب أن يكون الحشو الداخلي للجسر نصًا.',
            'bridges_denture.string' => 'يجب أن يكون طقم الجسر نصًا.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = $this->returnErrorMessage($errors->messages(), 422);

        throw new HttpResponseException($response);
    }
}
