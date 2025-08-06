<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'economic_activity_type_id' => 'nullable|exists:economic_activity_types,id',
            'identification_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('companies', 'identification_code')->ignore($this->route('company')->id),
            ],
            'visibility' => 'required|in:0,1',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ];
    }
}
