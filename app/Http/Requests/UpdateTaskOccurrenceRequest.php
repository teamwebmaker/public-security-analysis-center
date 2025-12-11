<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskOccurrenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_id' => ['required', 'exists:task_occurrence_statuses,id'],
            'branch_id_snapshot' => ['nullable', 'integer'],
            'branch_name_snapshot' => ['nullable', 'string', 'max:255'],
            'service_id_snapshot' => ['nullable', 'integer'],
            'service_name_snapshot' => ['nullable', 'string', 'max:255'],
            'requires_document' => ['nullable', 'boolean'],
            'visibility' => ['nullable', 'boolean'],
            'document' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:5120'],
            'delete_document' => ['nullable', 'boolean'],
            // explicitly disallow date fields from being mass-assigned
            'due_date' => ['prohibited'],
            'start_date' => ['prohibited'],
            'end_date' => ['prohibited'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'requires_document' => filter_var($this->input('requires_document'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            'visibility' => filter_var($this->input('visibility'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            'delete_document' => filter_var($this->input('delete_document'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        ]);
    }
}

