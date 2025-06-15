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
            "title.ka" => "required|string|max:200",
            "title.en" => "required|string|max:200",
            "description.ka" => "required|string|min:10",
            "description.en" => "required|string|min:10",
            "image" => "sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:5120",
            "certificate_image" =>
                "sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:5120",
            "video" => "nullable|url",
            "price" => "required|numeric|min:0",
            "duration" => "required|string|max:100",
            "address" => "required|string|max:225",
            "start_date" => "required|date|after_or_equal:today",
            "end_date" => "required|date|after_or_equal:start_date",
            "hour.start" => "required|date_format:H:i",
            "hour.end" => "required|date_format:H:i|after:hour.start",
            "days" => "required|array|min:1",
            "days.*" => "string",
            "visibility" => "required|in:1,0",
        ];
    }
}
