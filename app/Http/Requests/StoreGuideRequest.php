<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
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
            'sort_order' => ['required', 'integer', 'min:1', 'unique:guides,sort_order'],
        ];
    }
}
