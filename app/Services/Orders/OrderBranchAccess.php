<?php

namespace App\Services\Orders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class OrderBranchAccess
{
    public function forUser(User $user): Collection
    {
        $query = Branch::query()
            ->where('visibility', '1')
            ->with('company:id,name')
            ->orderBy('name');

        match ($user->getRoleName()) {
            User::ADMIN_ROLE, 'worker' => null,
            'company_leader' => $query->whereHas(
                'company.users',
                fn($users) => $users->where('users.id', $user->id)
            ),
            'responsible_person' => $query->whereHas(
                'users',
                fn($users) => $users->where('users.id', $user->id)
            ),
            default => $query->whereRaw('1 = 0'),
        };

        return $query->get();
    }

    public function idsForUser(User $user)
    {
        return $this->forUser($user)->pluck('id');
    }
}
