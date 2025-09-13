<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
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
            'full_name' => 'string|min:3|max:200',
            'subject' => 'string|min:3|max:200',
            'email' => 'required|email|min:5|max:500',
            'phone' => 'nullable|min:5|max:20',
            'message' => 'required|min:5',
            'service_ids' => 'nullable|array',
            'company_name' => 'nullable|string|min:3|max:200'
        ];
    }
}
