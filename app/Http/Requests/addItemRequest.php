<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use app\Traits\handleResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddItemRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'standard_quantity' => 'required|numeric|min:0',
            'minimum_quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'is_static' => 'required|boolean',
            // 'created_at' و 'updated_at' عادة ليست بحاجة للتحقق فيها هنا إذا كانت تُدار تلقائيًا
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'اسم العنصر مطلوب.',
            'name.string' => 'يجب أن يكون اسم العنصر نصًا.',
            'name.max' => 'اسم العنصر لا يجوز أن يتجاوز 255 حرفًا.',
            'standard_quantity.required' => 'الكمية القياسية مطلوبة.',
            'standard_quantity.numeric' => 'يجب أن تكون الكمية القياسية رقمًا.',
            'standard_quantity.min' => 'الكمية القياسية لا يمكن أن تكون أقل من 0.',
            'minimum_quantity.required' => 'الحد الأدنى للكمية مطلوب.',
            'minimum_quantity.numeric' => 'يجب أن تكون الحد الأدنى للكمية رقمًا.',
            'minimum_quantity.min' => 'الحد الأدنى للكمية لا يمكن أن تكون أقل من 0.',
            'unit.required' => 'وحدة القياس مطلوبة.',
            'unit.string' => 'يجب أن تكون وحدة القياس نصًا.',
            'unit.max' => 'وحدة القياس لا يجوز أن تتجاوز 50 حرفًا.',
            'is_static.required' => 'حالة الثبات مطلوبة.',
            'is_static.boolean' => 'حالة الثبات يجب أن تكون true أو false.',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = $this->returnErrorMessage($errors->messages(), 422);

        throw new HttpResponseException($response);
    }
}
