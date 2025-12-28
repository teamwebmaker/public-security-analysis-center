<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminNumberRequest extends FormRequest
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
        $adminNumber = $this->route('admin_number');

        return [
            'name' => ['required', 'string', 'max:100'],
            'phone' => [
                'required',
                'string',
                'max:30',
                Rule::unique('admin_numbers', 'phone')->ignore($adminNumber?->id),
            ],
        ];
    }
}
