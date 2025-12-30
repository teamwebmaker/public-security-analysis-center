<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSmsLogRequest extends FormRequest
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
            'provider' => ['required', 'string', 'max:100'],
            'provider_message_id' => ['nullable', 'string', 'max:191'],
            'destination' => ['required', 'string', 'max:50'],
            'content' => ['required', 'string', 'max:255'],
            'smsno' => ['required', 'integer', Rule::in([1, 2])],
            'status' => ['required', 'integer', Rule::in([0, 1, 2])],
            'provider_response' => ['nullable', 'json'],
            'sent_at' => ['nullable', 'date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('provider_response') === '') {
            $this->merge(['provider_response' => null]);
        }
    }
}
