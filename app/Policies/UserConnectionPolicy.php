<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserConnectionPolicy
{

    /**
     * Determine witch relations can be attached to the user based on role
     * @param \App\Models\User $user
     * @param string $relation
     * @return bool
     */
    public function canAttach(User $user, string $relation): bool
    {
        return match ($user->getRoleName()) {
            'company_leader' => in_array($relation, ['companies']),
            'responsible_person' => in_array($relation, ['branches', 'services']),
            'worker' => in_array($relation, ['tasks']),
            default => false,
        };
    }

    /**
     * Determine whether the user can view any models.
     */
    // public function viewAny(User $user): bool
    // {

    // }

    /**
     * Determine whether the user can view the model.
     */
    // public function view(User $user, User $model): bool
    // {

    // }

    /**
     * Determine whether the user can create models.
     */
    // public function create(User $user): bool
    // {
    // }

    /**
     * Determine whether the user can update the model.
     */
    // public function update(User $user, User $model): bool
    // {
    // }

    /**
     * Determine whether the user can delete the model.
     */
    // public function delete(User $user, User $model): bool
    // {
    // }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, User $model): bool
    // {

    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, User $model): bool
    // {

    // }
}
