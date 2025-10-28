<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentTemplateRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:200',
            'document' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
            'worker_ids' => 'nullable|array',
            'worker_ids.*' => 'exists:users,id',
            'visibility' => 'required|in:1,0',
        ];
    }
}
