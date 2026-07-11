<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManageOwnTaskAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->getRoleName() === 'worker';
    }

    public function rules(): array
    {
        return [
            'user_id' => ['prohibited'],
            'user_ids' => ['prohibited'],
            'user_ids.*' => ['prohibited'],
            'worker_id' => ['prohibited'],
        ];
    }
}
