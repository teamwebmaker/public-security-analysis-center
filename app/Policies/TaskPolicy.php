<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function create(User $user): bool
    {
        return $user->getRoleName() === 'worker';
    }

    public function viewActiveTasks(User $user): bool
    {
        return $user->getRoleName() === 'worker';
    }

    public function manageOwnAssignment(User $user, Task $task): bool
    {
        return $user->getRoleName() === 'worker' && $task->isActive();
    }

    public function workOn(User $user, Task $task): bool
    {
        if ($user->getRoleName() !== 'worker' || !$task->isActive()) {
            return false;
        }

        return $task->latestOccurrence()
            ->whereHas('workers', function ($query) use ($user) {
                $query->where('worker_id_snapshot', $user->id);
            })
            ->exists();
    }
}
