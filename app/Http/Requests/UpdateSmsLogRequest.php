<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSmsLogRequest extends FormRequest
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
            'destination' => ['required', 'string', 'max:50'],
            'content' => ['required', 'string', 'max:255'],
            'smsno' => ['required', 'integer', Rule::in([1, 2])],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('provider_response') === '') {
            $this->merge(['provider_response' => null]);
        }
    }
}
