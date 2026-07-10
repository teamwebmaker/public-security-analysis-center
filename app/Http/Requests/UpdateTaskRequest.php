<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateTaskRequest extends FormRequest
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
            'service_mode' => ['sometimes', 'in:existing,temporary'],
            'service_id' => ['nullable', 'required_without:temporary_service_name', 'exists:services,id'],
            'temporary_service_name' => ['nullable', 'required_without:service_id', 'string', 'max:255'],
            'branch_id' => ['required', 'exists:branches,id'],
            'branch_name_snapshot' => ['nullable', 'string', 'max:255'],

            'is_recurring' => ['required', 'boolean'],
            'recurrence_interval' => ['nullable', 'integer', 'min:1', 'required_if:is_recurring,true'],
            'requires_document' => ['nullable', 'boolean'],

            'visibility' => ['required', 'boolean'],
            'archived' => ['sometimes', 'boolean'],

            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->filled('service_id') && $this->filled('temporary_service_name')) {
                $message = 'აირჩიეთ არსებული სერვისი ან შეიყვანეთ დროებითი სერვისი, არა ორივე.';
                $validator->errors()->add('service_id', $message);
                $validator->errors()->add('temporary_service_name', $message);
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_recurring' => filter_var($this->input('is_recurring'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'requires_document' => filter_var($this->input('requires_document'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'temporary_service_name' => $this->filled('temporary_service_name')
                ? trim((string) $this->input('temporary_service_name'))
                : null,
        ]);
    }
}
