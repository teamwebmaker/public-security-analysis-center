<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
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
            // Title fields
            'title_ka.required' => 'გთხოვთ შეავსეთ სათაური (ქართულად).',
            'title_ka.min' => 'სათაური (ქართულად) უნდა შეიცავდეს მინიმუმ 3 სიმბოლოს.',
            'title_ka.max' => 'სათაური (ქართულად) არ უნდა აღემატებოდეს 200 სიმბოლოს.',

            'title_en.required' => 'გთხოვთ შეავსეთ სათაური (ინგლისურად).',
            'title_en.min' => 'სათაური (ინგლისურად) უნდა შეიცავდეს მინიმუმ 3 სიმბოლოს.',
            'title_en.max' => 'სათაური (ინგლისურად) არ უნდა აღემატებოდეს 200 სიმბოლოს.',

            // Description fields
            'description_ka.required' => 'გთხოვთ შეავსეთ აღწერა (ქართულად).',
            'description_ka.min' => 'აღწერა (ქართულად) უნდა შეიცავდეს მინიმუმ 10 სიმბოლოს.',

            'description_en.required' => 'გთხოვთ შეავსეთ აღწერა (ინგლისურად).',
            'description_en.min' => 'აღწერა (ინგლისურად) უნდა შეიცავდეს მინიმუმ 10 სიმბოლოს.',

            // Image field (optional in update)
            'image.image' => 'ატვირთული ფაილი უნდა იყოს სურათი.',
            'image.mimes' => 'სურათი უნდა იყოს JPG, JPEG, PNG ან WEBP ფორმატში.',
            'image.max' => 'სურათის ზომა არ უნდა აღემატებოდეს 2MB-ს.',

            // Visibility field
            'visibility.required' => 'გთხოვთ მიუთითოთ ხილვადობის სტატუსი.',
            'visibility.in' => 'არასწორი ხილვადობის მნიშვნელობა.',
        ];
    }
}