<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
            'title_ka' => 'required|string|min:3|max:200',
            'title_en' => 'required|string|min:3|max:200',
            'description_ka' => 'required|string|min:10',
            'description_en' => 'required|string|min:10',
            'image' => 'sometimes|image|mimes:jpg,jpeg,webp,png|max:2048',
            'visibility' => 'required|in:1,0',
            'sortable' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('services')
                    // Ignore the current service and validate rest of them
                    ->ignore($this->route('service'))
                    ->where(function ($query) {
                        return $query->where('service_category_id', $this->service_category_id);
                    }),
            ],

            'service_category_id' => ['nullable', 'integer', 'exists:service_categories,id'] // array good for complex validation
        ];
    }

    public function messages(): array
    {
        return [
            'sortable.unique' => 'ამ კატეგორიაში ეს რიგი უკვე დაკავებულია. გთხოვთ მიუთითეთ უნიკალური რიგის ნომერი.',
        ];
    }
}
