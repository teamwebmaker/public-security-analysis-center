<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
            'image' => 'required|image|mimes:jpg,jpeg,webp,png|max:2048',
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
            'title_ka.min' => 'სათაური (ქართულად) უნდა იყოს მინიმუმ 3 სიმბოლო.',
            'title_ka.max' => 'სათაური (ქართულად) არ უნდა აღემატებოდეს 200 სიმბოლოს.',

            'title_en.required' => 'გთხოვთ შეავსეთ სათაური (ინგლისურად).',
            'title_en.min' => 'სათაური (ინგლისურად) უნდა იყოს მინიმუმ 3 სიმბოლო.',
            'title_en.max' => 'სათაური (ინგლისურად) არ უნდა აღემატებოდეს 200 სიმბოლოს.',

            // Description fields
            'description_ka.required' => 'გთხოვთ შეავსეთ აღწერა (ქართულად).',
            'description_ka.min' => 'აღწერა (ქართულად) უნდა იყოს მინიმუმ 10 სიმბოლო.',

            'description_en.required' => 'გთხოვთ შეავსეთ აღწერა (ინგლისურად).',
            'description_en.min' => 'აღწერა (ინგლისურად) უნდა იყოს მინიმუმ 10 სიმბოლო.',

            // Image field
            'image.required' => 'გთხოვთ ატვირთეთ პროექტის სურათი.',
            'image.image' => 'ფაილი უნდა იყოს სურათი.',
            'image.mimes' => 'სურათი უნდა იყოს JPG, JPEG, PNG ან WEBP ფორმატში.',
            'image.max' => 'სურათი არ უნდა აღემატებოდეს 2MB-ს.',

            // Visibility field
            'visibility.required' => 'გთხოვთ აირჩიოთ ხილვადობა.',
            'visibility.in' => 'არასწორი ხილვადობის მნიშვნელობა.',
        ];
    }
}