<?php

namespace App\Policies;

use App\Models\TaskOccurrence;
use App\Models\User;

class TaskOccurrencePolicy
{
    public function uploadPaymentProof(User $user, TaskOccurrence $taskOccurrence): bool
    {
        return $taskOccurrence->payment_status !== 'paid'
            && $this->isConnectedResponsiblePerson($user, $taskOccurrence);
    }

    public function viewPaymentProof(User $user, TaskOccurrence $taskOccurrence): bool
    {
        return $user->isAdmin() || $this->isConnectedResponsiblePerson($user, $taskOccurrence);
    }

    public function markPaid(User $user, TaskOccurrence $taskOccurrence): bool
    {
        return $user->isAdmin();
    }

    private function isConnectedResponsiblePerson(User $user, TaskOccurrence $taskOccurrence): bool
    {
        if ($user->getRoleName() !== 'responsible_person') {
            return false;
        }

        $hasBranchAccess = $taskOccurrence->branch_id_snapshot !== null
            && $user->branches()->whereKey($taskOccurrence->branch_id_snapshot)->exists();

        if (! $hasBranchAccess) {
            return false;
        }

        return $taskOccurrence->service_id_snapshot === null
            || $user->services()->whereKey($taskOccurrence->service_id_snapshot)->exists();
    }
}
