<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password as RulesPassword;

class StoreUserRequest extends FormRequest
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
            'full_name' => 'required|string|min:2|max:100',
            'email' => 'nullable|email|unique:users,email|min:3|max:100',
            'phone' => 'required|numeric|digits_between:5,20|unique:users,phone',
            'password' => ['required', RulesPassword::min(6), 'confirmed'], // requires "password_confirmation" field
            'password_confirmation' => 'required|same:password',
            'role_id' => 'required|exists:roles,id',
            'company_ids' => 'nullable|array',
            'company_ids.*' => 'exists:companies,id',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ];
    }
}
