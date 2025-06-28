<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSyllabusRequest extends FormRequest
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
            'pdf' => 'required|mimes:pdf|max:2048',
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'visibility' => 'required|in:1,0',
        ];
    }
}
