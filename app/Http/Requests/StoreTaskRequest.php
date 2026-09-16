<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'service_mode' => ['sometimes', 'in:existing,temporary'],
            'service_id' => ['nullable', 'required_without:temporary_service_name', 'exists:services,id'],
            'temporary_service_name' => ['nullable', 'required_without:service_id', 'string', 'max:255'],
            'branch_id' => ['required', 'exists:branches,id'],
            'branch_name_snapshot' => ['nullable', 'string', 'max:255'],
            // 'document' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',

            'is_recurring' => ['required', 'boolean'],
            'recurrence_interval' => ['nullable', 'integer', 'min:1', 'required_if:is_recurring,true'],
            'due_date' => [
                'nullable',
                'date',
                Rule::requiredIf(fn() => ! $this->boolean('is_recurring')),
                Rule::prohibitedIf(fn() => $this->boolean('is_recurring')),
            ],

            // Task occurrences
            'requires_document' => ['nullable', 'boolean'],

            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['nullable', 'integer', 'exists:users,id'],

            'visibility' => ['required', 'boolean'],

        ];
    }

    public function messages(): array
    {
        return [
            'due_date.required' => 'ერთჯერადი საქმისთვის მიუთითეთ გადახდის ბოლო ვადა.',
            'due_date.date' => 'გადახდის ბოლო ვადა მიუთითეთ თარიღის სწორი ფორმატით.',
            'due_date.prohibited' => 'განმეორებადი საქმის გადახდის ბოლო ვადა ავტომატურად გამოითვლება.',
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
