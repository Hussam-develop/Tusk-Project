<?php

namespace App\Http\Requests;

use app\Traits\HandleResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    use HandleResponseTrait;
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
            'email'      => ['required', 'email', 'unique:' . $this->getTableName($guard) . ',email'],
            'guard'      => ['required', 'in:dentist,lab_manager'],
            'register_subscription_duration'      => ['required', 'integer'],

            // 'subscription_from_date'      => ['required', 'date'],
            // 'subscription_to_date'      => ["required", 'date', 'after:subscription_from_date'],

            'password'   => [
                'required',
                'string',
                'min:8',                  // الحد الأدنى للطول
                'regex:/[a-z]/',          // حرف صغير على الأقل
                'regex:/[A-Z]/',          // حرف كبير على الأقل
                'regex:/[0-9]/',          // رقم واحد على الأقل
                'regex:/[@$!%*#?&]/',     // رمز خاص واحد على الأقل
                'confirmed',           // تأكيد كلمة المرور],
            ],

        ];

        $labManagerRules = [
            'full_name'       => ['required', 'string', 'min:3', 'max:15'],
            'lab_name'        => ['required', 'string', 'max:30'],
            'lab_type'        => ['required', 'string', 'max:20'],

            'lab_phone' => [
                'required',
                // 'unique:' . $this->getTableName($guard) . ',lab_phone',
                function ($attribute, $value, $fail) {
                    $phones = json_decode($value, true);

                    if (!is_array($phones) || count($phones) <= 1) {
                        return $fail('يجب إدخال رقم واحد على الأقل');
                    }

                    foreach ($phones as $phone) {
                        if (!preg_match('/^[0-9]{10}$/', $phone)) {
                            return $fail('كل رقم هاتف يجب أن يتكون من 10 أرقام فقط.');
                        }
                    }
                }
            ],
            'lab_province'    => ['required', 'string', 'max:15'],
            'lab_address'     => ['required', 'string', 'max:100'],

            'work_from_hour' => ['required', 'date_format:H:i'],
            'work_to_hour'   => ['required', 'date_format:H:i', 'after:work_from_hour'],

        ];
        $dentistRules = [
            'first_name' => ['required', 'string', 'min:3', 'max:15'],
            'last_name'  => ['required', 'string', 'min:3', 'max:30'],
            'phone'      => ['required', 'string', 'size:10', 'regex:/^[0-9]+$/', 'starts_with:09', 'unique:' . $this->getTableName($guard) . ',phone'],
            'address'    => ['required', 'string', 'min:10'],
            'image'    => ['nullable', 'file']
        ];

        $dentistTimesRules = [
            'السبت' => ['nullable'],
            'الأحد' => ['nullable'],
            'الاثنين' => ['nullable'],
            'الثلاثاء' => ['nullable'],
            'الأربعاء' => ['nullable'],
            'الخميس' => ['nullable'],
            'الجمعة' => ['nullable'],
        ];

        return match ($guard) {
            'dentist'     => array_merge($commonRules, $dentistRules, $dentistTimesRules),
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
            'phone.*.size' => ' يجب أن يكون رقم العيادة مكوّن من 10 أرقام حصراً ',
            'phone.*.regix' => 'يجب أن يكون رقم العيادة مكون من أرقام فقط',
            'phone.*.starts_with' => ' يجب أن يبدأ رقم العيادة بـ 09 حصراً ',
            'phone.unique' => 'هذا رقم العيادة مستخدم سابقاً. يجب إدخال رقم آخر',

            'guard.required'      => 'نوع المستخدم مطلوب.',
            'guard.in'            => 'يجب أن يكون نوع المستخدم طبيب أسنان أو مدير مخبر.',
            'address.required'       => 'عنوان العيادة مطلوب.',
            'image.file'       => 'الصورة يجب أن تكون ملف',
            //'lab_register_date.required' => 'تاريخ تسجيل المخبر مطلوب.',
            // 'lab_register_date.date'     => 'يجب أن يكون تاريخ تسجيل المخبر تاريخًا صالحًا.',
            //'lab_logo.required'      => 'شعار المخبر مطلوب.',
            'lab_name.required'      => 'اسم المخبر مطلوب.',
            'lab_province.required'  => 'المحافظة التي يقع فيها المخبر مطلوبة.',
            'lab_address.required'   => 'عنوان المخبر مطلوب.',
            'lab_phone.*.required'     => 'هاتف المخبر مطلوب.',
            'lab_phone.*.size' => ' يجب أن يكون رقم مخبر التعويضات مكوّن من 10 أرقام حصراً ',
            'lab_phone.*.regix' => 'يجب أن يكون رقم مخبر التعويضات مكون من أرقام فقط',
            'lab_phone.*.starts_with' => ' يجب أن يبدأ رقم مخبر التعويضات بـ 09 حصراً ',
            'lab_phone.*.unique' => 'هذا رقم مخبر التعويضات مستخدم سابقاً. يجب إدخال رقم آخر',
            'lab_type.required'      => 'نوع المخبر مطلوب.',
            'work_from_hour.required' => 'ساعة افتتاح المخبر مطلوبة.',
            'work_to_hour.required'   => 'ساعة إغلاق المخبر مطلوبة.',
            'work_to_hour.date_format' => 'ساعة الإغلاق يجب أن تكون بصيغة (ساعة:دقيقة) مثل 17:00.',
            'work_to_hour.after' => 'ساعة الإغلاق يجب أن تكون بعد ساعة الافتتاح.',
            'lab_address.max' => 'العنوان يجب ألا يتجاوز 100 حرف ( بما في ذلك المسافات )',

            'register_subscription_duration.required' => 'يجب إدخال مدة الاشتراك ',

            // 'subscription_from_date.required' => 'يجب إدخال تاريخ بداية الاشتراك ',
            // 'subscription_from_date.date' => 'تاريخ بداية الاشتراك ليس بالصيغة القياسية للتاريخ',
            // 'subscription_to_date.required' => 'يجب إدخال تاريخ نهاية الاشتراك ',
            // 'subscription_to_date.date' => 'تاريخ نهاية الاشتراك ليس بالصيغة القياسية للتاريخ',
            // 'subscription_to_date.after' => 'تاريخ نهاية الاشتراك يجب أن يكون بعد تاريخ بداية الاشتراك',

        ];
    }

    private function getTableName(string $guard): string
    {
        return match ($guard) {
            'dentist'     => 'dentists',
            'lab_manager' => 'lab_managers',
        };
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = $this->returnErrorMessage($errors->messages(), 422);

        throw new HttpResponseException($response);
    }
}
