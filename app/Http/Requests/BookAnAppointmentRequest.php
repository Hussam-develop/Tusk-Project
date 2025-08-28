<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookAnAppointmentRequest extends FormRequest
{

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
            'patient_name'   => ['required', 'string'],
            'patient_phone'  => ['required', 'string'],
            'date'           => ['required', 'date'],
            'time_from'      => ['required', 'date_format:H:i'],
            'time_to'        => ['required', 'date_format:H:i', 'after:time_from'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_name.required'   => 'اسم المريض مطلوب.',
            'patient_name.string'     => 'اسم المريض يجب أن يكون نصاً.',

            'patient_phone.required'  => 'رقم هاتف المريض مطلوب.',
            'patient_phone.string'    => 'رقم الهاتف يجب أن يكون نصاً.',

            'date.required'           => 'تاريخ الموعد مطلوب.',
            'date.date'               => 'يرجى إدخال تاريخ صالح.',

            'time_from.required'      => 'وقت بداية الموعد مطلوب.',
            'time_from.date_format'   => 'صيغة وقت البداية يجب أن تكون على الشكل HH:MM.',

            'time_to.required'        => 'وقت نهاية الموعد مطلوب.',
            'time_to.date_format'     => 'صيغة وقت النهاية يجب أن تكون على الشكل HH:MM.',
            'time_to.after'           => 'وقت النهاية يجب أن يكون بعد وقت البداية.',
        ];
    }
}
