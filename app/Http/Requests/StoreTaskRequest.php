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
            'status_id' => ['required', 'exists:task_statuses,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'branch_name' => ['nullable', 'string', 'max:255'],

            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],


            'visibility' => ['required', 'boolean'],
            'archived' => ['sometimes', 'boolean'],
        ];
    }
}
