<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
            'service_id' => ['required', 'exists:services,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'branch_name_snapshot' => ['nullable', 'string', 'max:255'],
            // 'document' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',

            'is_recurring' => ['required', 'boolean'],
            'recurrence_interval' => ['nullable', 'integer', 'min:1', 'max:31', 'required_if:is_recurring,true'],

            // Task occurrences
            'requires_document' => ['nullable', 'boolean'],

            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['nullable', 'integer', 'exists:users,id'],

            'visibility' => ['required', 'boolean'],

        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_recurring' => filter_var($this->input('is_recurring'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'requires_document' => filter_var($this->input('requires_document'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
        ]);
    }
}
