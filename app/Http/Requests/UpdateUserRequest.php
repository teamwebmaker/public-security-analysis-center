<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as RulesPassword;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user')->id;

        $rules = [
            'full_name' => 'required|string|min:2|max:100',
            'email' => [
                'nullable',
                'email',
                'min:3',
                'max:100',
                Rule::unique('users')->ignore($userId)
            ],
            'phone' => [
                'required',
                'numeric',
                'digits_between:5,20',
                Rule::unique('users')->ignore($userId)
            ],
            'role_id' => 'required|exists:roles,id',
            'company_ids' => 'nullable|array',
            'company_ids.*' => 'exists:companies,id',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
        ];

        // Only validate password if it's provided
        if ($this->filled('password')) {
            $rules['password'] = ['sometimes', RulesPassword::min(6), 'confirmed'];
            $rules['password_confirmation'] = ['sometimes', 'same:password'];
        }

        return $rules;
    }
}
