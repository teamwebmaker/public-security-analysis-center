<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGuideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $guide = $this->route('guide');

        return [
            'name' => ['required', 'string', 'max:255'],
            'link' => [
                'required',
                'string',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $isUrl = filter_var($value, FILTER_VALIDATE_URL) !== false;
                    $isInternalPath = preg_match('/^\/[^\s]*$/', (string) $value) === 1;

                    if (!$isUrl && !$isInternalPath) {
                        $fail('ბმული უნდა იყოს სრული URL ან შიდა მისამართი, მაგალითად /admin/tasks.');
                    }
                },
            ],
            'sort_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('guides', 'sort_order')->ignore($guide?->id),
            ],
        ];
    }
}
