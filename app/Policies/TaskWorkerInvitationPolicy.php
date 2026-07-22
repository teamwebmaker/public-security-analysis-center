<?php

namespace App\Policies;

use App\Models\TaskWorkerInvitation;
use App\Models\User;

class TaskWorkerInvitationPolicy
{
    public function respond(User $user, TaskWorkerInvitation $invitation): bool
    {
        return $user->getRoleName() === 'worker'
            && (int) $invitation->invited_worker_id === (int) $user->id
            && $invitation->status === TaskWorkerInvitation::STATUS_PENDING;
    }
}
