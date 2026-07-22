<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskWorkerInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');

        return $task instanceof Task
            && ($this->user()?->can('inviteWorkers', $task) ?? false);
    }

    public function rules(): array
    {
        return [
            'invited_worker_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
