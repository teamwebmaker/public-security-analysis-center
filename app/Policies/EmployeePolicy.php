<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    private const VIEW_ROLES = [
        User::ADMIN_ROLE,
        'company_leader',
        'responsible_person',
        'worker',
    ];

    private const CREATE_ROLES = [
        User::ADMIN_ROLE,
        'company_leader',
        'responsible_person',
    ];

    public function viewAny(User $user): bool
    {
        return in_array($user->getRoleName(), self::VIEW_ROLES, true);
    }

    public function view(User $user, Employee $employee): bool
    {
        return Employee::query()
            ->visibleTo($user)
            ->whereKey($employee->getKey())
            ->exists();
    }

    public function create(User $user): bool
    {
        return in_array($user->getRoleName(), self::CREATE_ROLES, true);
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->isAdmin()
            || (int) $employee->created_by_user_id === (int) $user->id;
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $this->update($user, $employee);
    }

    public function viewPersonalDetails(User $user, Employee $employee): bool
    {
        if ($user->isAdmin() || (int) $employee->created_by_user_id === (int) $user->id) {
            return true;
        }

        return $employee->personal_details_visible && $this->view($user, $employee);
    }

    public function changePersonalDetailsVisibility(User $user, Employee $employee): bool
    {
        return (int) $employee->created_by_user_id === (int) $user->id;
    }
}
