<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;

class IncidentPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->getRoleName(), [
            User::ADMIN_ROLE,
            'worker',
            'company_leader',
            'responsible_person',
        ], true);
    }

    public function view(User $user, Incident $incident): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->getRoleName() === 'worker') {
            return (int) $incident->created_by_user_id === (int) $user->id;
        }

        return $incident->userParticipants()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->getRoleName() === 'worker';
    }

    public function update(User $user, Incident $incident): bool
    {
        return $user->isAdmin()
            || (
                $user->getRoleName() === 'worker'
                && (int) $incident->created_by_user_id === (int) $user->id
            );
    }

    public function sign(User $user, Incident $incident): bool
    {
        return in_array($user->getRoleName(), ['company_leader', 'responsible_person'], true)
            && $incident->userParticipants()->where('user_id', $user->id)->exists();
    }

    public function manageExternalSignatures(User $user, Incident $incident): bool
    {
        return $this->update($user, $incident);
    }
}
