<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEconomicActivityTypeRequest extends FormRequest
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
            Rule::unique('economic_activity_types', 'name')->ignore($this->route('economic_activity_type')->id),

            'display_name' => ['required', 'string', 'max:100', Rule::unique('economic_activity_types', 'display_name')->ignore($this->route('economic_activity_type')->id)]
        ];
    }
}
