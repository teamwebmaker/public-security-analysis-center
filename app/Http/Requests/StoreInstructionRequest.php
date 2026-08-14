<?php

namespace App\Http\Requests;

use App\Models\Instruction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInstructionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
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
            'link' => 'nullable|url|min:10',
            'document' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
            'worker_ids' => 'nullable|array',
            'worker_ids.*' => 'distinct|exists:users,id',
            'visibility' => 'required|in:1,0',
            'document_visibility' => ['required', Rule::in([
                Instruction::VISIBILITY_PRIVATE,
                Instruction::VISIBILITY_PUBLIC,
            ])],
        ];
    }
}
