<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgramsRequest extends FormRequest
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
            "title_ka" => "required|string|max:200",
            "title_en" => "required|string|max:200",
            "description_ka" => "required|string|min:10",
            "description_en" => "required|string|min:10",
            "image" => "sometimes|image|mimes:jpeg,png,jpg,webp|max:2048",
            "certificate_image" => "nullable|image|mimes:jpeg,png,jpg,webp|max:2048",
            "video" => "nullable|url",
            "price" => "required|numeric|min:0",
            "duration" => "required|string|max:100",
            "address" => "required|string|max:225",
            "start_date" => "required|date|after_or_equal:today",
            "end_date" => "required|date|after_or_equal:start_date",
            "hour_start" => "required|date_format:H:i",
            "hour_end" => "required|date_format:H:i|after:hour_start",
            "days" => "required|array|min:1",
            "days.*" => "string",
            "visibility" => "required|in:1,0",
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
            'title_ka.max' => 'სათაური (ქართულად) არ უნდა აღემატებოდეს 200 სიმბოლოს.',
            'title_en.required' => 'გთხოვთ შეავსეთ სათაური (ინგლისურად).',
            'title_en.max' => 'სათაური (ინგლისურად) არ უნდა აღემატებოდეს 200 სიმბოლოს.',

            // Description fields
            'description_ka.required' => 'გთხოვთ შეავსეთ აღწერა (ქართულად).',
            'description_ka.min' => 'აღწერა (ქართულად) უნდა იყოს მინიმუმ 10 სიმბოლო.',
            'description_en.required' => 'გთხოვთ შეავსეთ აღწერა (ინგლისურად).',
            'description_en.min' => 'აღწერა (ინგლისურად) უნდა იყოს მინიმუმ 10 სიმბოლო.',

            // Image fields
            'image.image' => 'პროგრამის სურათი უნდა იყოს სურათის ფაილი.',
            'image.mimes' => 'პროგრამის სურათი უნდა იყოს JPG, JPEG, PNG ან WEBP ფორმატში.',
            'image.max' => 'პროგრამის სურათი არ უნდა აღემატებოდეს 2MB-ს.',
            'certificate_image.image' => 'სერტიფიკატის სურათი უნდა იყოს სურათის ფაილი.',
            'certificate_image.mimes' => 'სერტიფიკატის სურათი უნდა იყოს JPG, JPEG, PNG ან WEBP ფორმატში.',
            'certificate_image.max' => 'სერტიფიკატის სურათი არ უნდა აღემატებოდეს 2MB-ს.',

            // Video field
            'video.url' => 'გთხოვთ მიუთითოთ სწორი ვიდეოს URL მისამართი.',

            // Price field
            'price.required' => 'გთხოვთ მიუთითოთ ფასი.',
            'price.numeric' => 'ფასი უნდა იყოს რიცხვითი მნიშვნელობა.',
            'price.min' => 'ფასი არ შეიძლება იყოს უარყოფითი.',

            // Duration field
            'duration.required' => 'გთხოვთ მიუთითოთ ხანგრძლივობა.',
            'duration.max' => 'ხანგრძლივობა არ უნდა აღემატებოდეს 100 სიმბოლოს.',

            // Address field
            'address.required' => 'გთხოვთ მიუთითოთ მისამართი.',
            'address.max' => 'მისამართი არ უნდა აღემატებოდეს 225 სიმბოლოს.',

            // Date fields
            'start_date.required' => 'გთხოვთ მიუთითოთ დაწყების თარიღი.',
            'start_date.date' => 'არასწორი დაწყების თარიღის ფორმატი.',
            'start_date.after_or_equal' => 'დაწყების თარიღი არ შეიძლება იყოს წარსულში.',
            'end_date.required' => 'გთხოვთ მიუთითოთ დასრულების თარიღი.',
            'end_date.date' => 'არასწორი დასრულების თარიღის ფორმატი.',
            'end_date.after_or_equal' => 'დასრულების თარიღი უნდა იყოს დაწყების თარიღის შემდეგ ან ტოლი.',

            // Time fields
            'hour_start.required' => 'გთხოვთ მიუთითოთ დაწყების დრო.',
            'hour_start.date_format' => 'დაწყების დრო უნდა იყოს HH:MM ფორმატში.',
            'hour_end.required' => 'გთხოვთ მიუთითოთ დასრულების დრო.',
            'hour_end.date_format' => 'დასრულების დრო უნდა იყოს HH:MM ფორმატში.',
            'hour_end.after' => 'დასრულების დრო უნდა იყოს დაწყების დროის შემდეგ.',

            // Days field
            'days.required' => 'გთხოვთ აირჩიოთ მინიმუმ ერთი დღე.',
            'days.min' => 'გთხოვთ აირჩიოთ მინიმუმ ერთი დღე.',

            // Visibility field
            'visibility.required' => 'გთხოვთ აირჩიოთ ხილვადობა.',
            'visibility.in' => 'არასწორი ხილვადობის მნიშვნელობა.',
        ];
    }
}