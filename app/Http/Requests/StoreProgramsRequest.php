<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramsRequest extends FormRequest
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
            'title_ka' => 'required|string|max:200',
            'title_en' => 'required|string|max:200',
            'description_ka' => 'required|string|min:10',
            'description_en' => 'required|string|min:10',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'certificate_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'video' => 'nullable|url',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|string|max:100',
            'address' => 'required|string|max:225',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'hour_start' => 'required|date_format:H:i',
            'hour_end' => 'required|date_format:H:i|after:hour_start',
            'days' => 'required|array|min:1',
            'days.*' => 'string',
            'visibility' => 'required|in:1,0',
        ];
    }
}