<?php

namespace App\Services\Employees;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class EmployeeBranchAccess
{
    public function forUser(User $user, ?Employee $employee = null): Collection
    {
        $currentBranchIds = $employee && $user->can('update', $employee)
            ? $employee->branches()->pluck('branches.id')
            : collect();

        $query = Branch::query()
            ->with('company:id,name')
            ->orderBy('name');

        match ($user->getRoleName()) {
            User::ADMIN_ROLE => null,
            'company_leader' => $query->where(function ($query) use ($user, $currentBranchIds) {
                $query->whereHas(
                    'company.users',
                    fn ($users) => $users->where('users.id', $user->id)
                )->when(
                    $currentBranchIds->isNotEmpty(),
                    fn ($query) => $query->orWhereIn('branches.id', $currentBranchIds)
                );
            }),
            'responsible_person' => $query->where(function ($query) use ($user, $currentBranchIds) {
                $query->whereHas(
                    'users',
                    fn ($users) => $users->where('users.id', $user->id)
                )->when(
                    $currentBranchIds->isNotEmpty(),
                    fn ($query) => $query->orWhereIn('branches.id', $currentBranchIds)
                );
            }),
            default => $query->whereRaw('1 = 0'),
        };

        $query->where(function ($query) use ($currentBranchIds) {
            $query->where('visibility', '1');

            if ($currentBranchIds->isNotEmpty()) {
                $query->orWhereIn('branches.id', $currentBranchIds);
            }
        });

        return $query->get();
    }

    public function branchIdsFor(User $user, ?Employee $employee = null)
    {
        return $this->forUser($user, $employee)->pluck('id');
    }

    public function companyIdsFor(User $user, ?Employee $employee = null)
    {
        return $this->forUser($user, $employee)->pluck('company_id')->unique()->values();
    }
}
