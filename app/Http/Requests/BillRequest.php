<?php

namespace App\Http\Requests;

use app\Traits\HandleResponseTrait;
use App\Rules\DateAfterOneDayAtLeastRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BillRequest extends FormRequest
{
    use HandleResponseTrait;

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
            'dentist_id' => ['required', 'exists:dentists,id'],
            'date_from' => ['required', "date"],
            'date_to' => ['required', "date", "after:date_from" /*,new DateAfterOneDayAtLeastRule($this->date_from)*/],

            // 'note' => ["string", "min:3"], not needed until now

        ];
    }
    public function messages(): array
    {
        return [
            'dentist_id.required'      => 'رقم تعريف الطبيب مطلوب',
            'dentist_id.exists'      => 'رقم تعريف الطبيب لم يتم العثور عليه',

            'date_from.required'           => 'تاريخ بداية الفاتورة مطلوب.',
            'date_from.date'      => 'تاريخ بداية الفاتورة ليس من النمط تاريخ',

            'date_to.required'        => 'تاريخ نهاية الفاتورة مطلوب .',
            'date_to.date'            => 'تاريخ نهاية الفاتورة ليس من النمط تاريخ',
            'date_to.after'            => 'يجب أن يكون تاريخ نهاية الفاتورة بعد يوم واحد على الأقل من تاريخ بداية الفاتورة'

        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = $this->returnErrorMessage($errors->messages(), 422);

        throw new HttpResponseException($response);
    }
}
