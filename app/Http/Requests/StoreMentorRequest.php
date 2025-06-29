<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMentorRequest extends FormRequest
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
            'full_name' => 'required|string|min:3|max:200',
            'description_ka' => 'nullable|string|min:10|max:250',
            'description_en' => 'nullable|string|min:10|max:250',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'visibility' => 'required|in:1,0',
        ];
    }
}
