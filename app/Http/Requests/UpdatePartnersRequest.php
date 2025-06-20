<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePartnersRequest extends FormRequest
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
            'title' => 'required|string|min:3|max:200',
            'link' => 'required|url|min:10',
            'image' => 'sometimes|image|mimes:jpg,jpeg,webp,png|max:2048',
            'visibility' => 'required|in:1,0',
        ];
    }

    /**
     * Custom error messages for validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Title field
            'title.required' => 'გთხოვთ შეავსეთ სათაური.',
            'title.min' => 'სათაური უნდა იყოს მინიმუმ 3 სიმბოლო.',
            'title.max' => 'სათაური არ უნდა აღემატებოდეს 200 სიმბოლოს.',

            // Link field
            'link.required' => 'გთხოვთ შეავსეთ ბმული.',
            'link.url' => 'გთხოვთ მიუთითოთ სწორი URL მისამართი.',
            'link.min' => 'ბმული უნდა იყოს მინიმუმ 10 სიმბოლო.',

            // Image field (note: 'sometimes' means it's only validated if present)
            'image.image' => 'ფაილი უნდა იყოს სურათი.',
            'image.mimes' => 'სურათი უნდა იყოს JPG, JPEG, PNG ან WEBP ფორმატში.',
            'image.max' => 'სურათი არ უნდა აღემატებოდეს 2MB-ს.',

            // Visibility field
            'visibility.required' => 'გთხოვთ აირჩიოთ ხილვადობა.',
            'visibility.in' => 'არასწორი ხილვადობის მნიშვნელობა.',
        ];
    }
}