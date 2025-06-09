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
            'title'             => 'required|string|max:200',
            'description'       => 'required|string|min:10',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'certificate_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video'             => 'nullable|url',
            'price'             => 'required|numeric|min:0',
            'duration'          => 'required|string|max:100',
            'address'           => 'required|string|max:225',
            'start_date'        => 'required|date|after_or_equal:today',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'start_time'        => 'required|date_format:H:i',
            'end_time'          => 'required|date_format:H:i|after:start_time',
            'days'              => 'required|array|min:1',
            'visibility'        => 'required|in:1,0',
        ];
    }
}
