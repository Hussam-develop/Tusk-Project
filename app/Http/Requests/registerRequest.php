<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class registerRequest extends FormRequest
{
    /*
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /*
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $guard = $this->input('guard');

        $commonRules = [
            'first_name' => ['required', 'string', 'min:3', 'max:15'],
            'last_name'  => ['required', 'string', 'min:3', 'max:255'],
            'email'      => ['required', 'email', 'unique:' . $this->getTableName($guard) . ',email'],
            'guard'      => ['required', 'in:dentist,lab_manager'],
            'password'   => [
                'required',
                'string',
                'min:8',                  // الحد الأدنى للطول
                'regex:/[a-z]/',          // حرف صغير على الأقل
                'regex:/[A-Z]/',          // حرف كبير على الأقل
                'regex:/[0-9]/',          // رقم واحد على الأقل
                'regex:/[@$!%*#?&]/',     // رمز خاص واحد على الأقل
                'confirmed',              // تأكيد كلمة المرور],
            ],

        ];

        $labManagerRules = [
            'lab_name'      => ['required', 'string', 'max:30'],
            'lab_type'      => ['required', 'string', 'max:20'],
            'lab_from_hour' => ['required', 'date_format:H:i'],
            'lab_to_hour'   => ['required', 'date_format:H:i', 'after:lab_from_hour'],
            'lab_phone'     => ['required', 'size:2'],
            'lab_province'  => ['required', 'string', 'max:15'],
            'lab_address'   => ['required', 'string', 'max:60'],

        ];
        $dentistRules = [
            'phone'      => ['required', 'string', 'max:20'],
            'address'    => ['required', 'string', 'min:10'],
        ];

        return match ($guard) {
            'dentist'     => array_merge($commonRules, $dentistRules),
            'lab_manager' => array_merge($commonRules, $labManagerRules),
        };
    }
    public function messages(): array
    {
        return [
            'first_name.required' => 'الاسم الأول مطلوب.',
            'last_name.required'  => 'اسم العائلة مطلوب.',
            'email.required'      => 'البريد الإلكتروني مطلوب.',
            'email.email'         => 'يرجى إدخال عنوان بريد إلكتروني صالح.',
            'email.unique'        => 'هذا البريد الإلكتروني مسجّل بالفعل.',
            'password.required'   => 'كلمة المرور مطلوبة.',
            'password.min'        => 'يجب أن تكون كلمة المرور 6 أحرف على الأقل.',
            'password.regex'     => 'كلمة المرور يجب أن تحتوي على حرف كبير، حرف صغير، رقم، ورمز خاص.',
            'password.confirmed'  => 'تأكيد كلمة المرور غير مطابق.',
            'phone.required'      => 'رقم الهاتف مطلوب.',
            'guard.required'      => 'نوع المستخدم مطلوب.',
            'guard.in'            => 'يجب أن يكون نوع المستخدم طبيب أسنان أو مدير مخبر.',
            'address.required'       => 'عنوان العيادة مطلوب.',
            //'lab_register_date.required' => 'تاريخ تسجيل المخبر مطلوب.',
            // 'lab_register_date.date'     => 'يجب أن يكون تاريخ تسجيل المخبر تاريخًا صالحًا.',
            //'lab_logo.required'      => 'شعار المخبر مطلوب.',
            'lab_name.required'      => 'اسم المختبر مطلوب.',
            'lab_province.required'  => 'المحافظة التي يقع فيها المختبر مطلوبة.',
            'lab_address.required'   => 'عنوان المختبر مطلوب.',
            'lab_phone.required'     => 'هاتف المختبر مطلوب.',
            'lab_type.required'      => 'نوع المختبر مطلوب.',
            'lab_from_hour.required' => 'ساعة افتتاح المختبر مطلوبة.',
            'lab_to_hour.required'   => 'ساعة إغلاق المختبر مطلوبة.',
            'lab_to_hour.date_format' => 'ساعة الإغلاق يجب أن تكون بصيغة (ساعة:دقيقة) مثل 17:00.',
            'lab_to_hour.after' => 'ساعة الإغلاق يجب أن تكون بعد ساعة الافتتاح.',

        ];
    }

    private function getTableName(string $guard): string
    {
        return match ($guard) {
            'dentist'     => 'dentists',
            'lab_manager' => 'lab_managers',
        };
    }
}
