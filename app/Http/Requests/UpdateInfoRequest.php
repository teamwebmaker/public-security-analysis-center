<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInfoRequest extends FormRequest
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
            'experience' => 'nullable|numeric|min:0',
            'graduates' => 'nullable|numeric|min:0',
            'phone' => 'required|string|min:7|max:20',
            'email' => 'required|email|max:255',
        ];

    }
}
