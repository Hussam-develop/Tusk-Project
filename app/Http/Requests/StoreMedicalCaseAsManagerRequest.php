<?php

namespace App\Http\Requests;

use app\Traits\handleResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreMedicalCaseAsManagerRequest extends FormRequest
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
            'expected_delivery_date' => ['required', 'date', "date_format:Y-m-d", 'after:now'],
            'notes' => ['string', "nullable"],

            /////////////
            'dentist_id'      => ['required', "integer", 'exists:dentists,id'],
            'patient_full_name'      => ['required', 'string', 'max:50'],
            'patient_phone'      => ['required', 'string', 'regex:/^[0-9]+$/', 'starts_with:09', 'size:10'],
            'patient_birthdate'      => ['required', 'date', 'before:today'],
            'patient_gender'           => ['required', 'in:ذكر,أنثى'],
            'is_smoker'        => ['required', 'boolean'],
            'shade' => ['required', 'string'],
            'need_trial' => ['required', 'in:0,1'],
            'repeat' => ['required', 'in:0,1'],
            /////////////

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

            /////////////


        ];
        return $rules;
    }
    public function messages()
    {
        return [

            'expected_delivery_date.required' => 'يرجى إدخال تاريخ التسليم المتوقع.',
            'expected_delivery_date.date' => 'تاريخ التسليم المتوقع يجب أن يكون تاريخاً صحيحاً.',
            'expected_delivery_date.date_format' => 'تنسيق تاريخ التسليم المتوقع يجب أن يكون على الشكل: سنة-شهر-يوم.',
            'expected_delivery_date.after' => 'تاريخ التسليم المتوقع يجب أن يكون بعد تاريخ اليوم.',

            'notes.string' => 'الملاحظات يجب أن تكون نصاً.',

            'dentist_id.required' => 'يرجى اختيار اسم الطبيب.',
            'dentist_id.integer' => 'رقم الطبيب يجب أن يكون عدداً صحيحاً.',
            'dentist_id.exists' => 'الطبيب المختار غير موجود في السجلات.',

            'patient_full_name.required' => 'يرجى إدخال اسم المريض الكامل.',
            'patient_full_name.string' => 'اسم المريض يجب أن يكون نصاً.',
            'patient_full_name.max' => 'اسم المريض يجب ألا يتجاوز 30 حرفاً.',

            'patient_phone.required' => 'يرجى إدخال رقم هاتف المريض.',
            'patient_phone.string' => 'رقم الهاتف يجب أن يكون نصاً.',
            'patient_phone.regex' => 'يرجى إدخال رقم هاتف صالح (10 أرقام حصراً).',
            'patient_phone.starts_with' => 'يرجى إدخال رقم هاتف صالح يبدأ ب 09 حصراً.',
            'patient_phone.size' => 'يرجى إدخال رقم هاتف صالح (10 أرقام حصراً).',

            'patient_birthdate.required' => 'يرجى إدخال تاريخ ميلاد المريض.',
            'patient_birthdate.date' => 'تاريخ الميلاد يجب أن يكون تاريخاً صحيحاً.',
            'patient_birthdate.before' => 'تاريخ الميلاد يجب أن يكون قبل تاريخ اليوم.',

            'patient_gender.required' => 'يرجى تحديد جنس المريض.',
            'patient_gender.in' => 'يجب أن يكون جنس المريض إما "ذكر" أو "أنثى".',

            'is_smoker.required' => 'يرجى تحديد ما إذا كان المريض مدخناً.',
            'is_smoker.boolean' => 'قيمة التدخين يجب أن تكون صحيحة (صح أو خطأ).',

            'shade.required' => 'يرجى إدخال لون السن المطلوب.',
            'shade.string' => 'لون السن يجب أن يكون نصاً.',

            'need_trial.required' => 'يرجى تحديد ما إذا كان هناك حاجة لتجربة.',
            'need_trial.in' => 'القيمة يجب أن تكون إما 0 أو 1.',

            'repeat.required' => 'يرجى تحديد ما إذا كان الطلب مكرر.',
            'repeat.in' => 'القيمة يجب أن تكون إما 0 أو 1.',


            // 'teeth_crown.string' => 'يجب أن يكون تاج الأسنان نصًا.',
            // 'teeth_pontic.string' => 'يجب أن يكون الجسر نصًا.',
            // 'teeth_implant.string' => 'يجب أن يكون زرع الأسنان نصًا.',
            // 'teeth_veneer.string' => 'يجب أن يكون قشرة الأسنان نصًا.',
            // 'teeth_inlay.string' => 'يجب أن يكون الحشو الداخلي للأسنان نصًا.',
            // 'teeth_denture.string' => 'يجب أن يكون طقم الأسنان نصًا.',

            // 'bridges_crown.string' => 'يجب أن يكون تاج الجسر نصًا.',
            // 'bridges_pontic.string' => 'يجب أن يكون الجسر نصًا.',
            // 'bridges_implant.string' => 'يجب أن يكون زرع الجسر نصًا.',
            // 'bridges_veneer.string' => 'يجب أن يكون قشرة الجسر نصًا.',
            // 'bridges_inlay.string' => 'يجب أن يكون الحشو الداخلي للجسر نصًا.',
            // 'bridges_denture.string' => 'يجب أن يكون طقم الجسر نصًا.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = $this->returnErrorMessage($errors->messages(), 422);

        throw new HttpResponseException($response);
    }
}
