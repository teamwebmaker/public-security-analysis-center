<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Validation\Rule;

class StoreWorkerTaskRequest extends StoreTaskRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Task::class) ?? false;
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'service_id' => [
                'nullable',
                'required_without:temporary_service_name',
                Rule::exists('services', 'id')->where('visibility', '1'),
            ],
            'branch_id' => [
                'required',
                Rule::exists('branches', 'id')->where('visibility', '1'),
            ],
            'branch_name_snapshot' => ['prohibited'],
            'visibility' => ['prohibited'],
            'archived' => ['prohibited'],
            'user_id' => ['prohibited'],
            'user_ids' => ['prohibited'],
            'user_ids.*' => ['prohibited'],
        ]);
    }
}
