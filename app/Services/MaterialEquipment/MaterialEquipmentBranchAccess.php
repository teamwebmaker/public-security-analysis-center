<?php

namespace App\Services\MaterialEquipment;

use App\Models\Branch;
use App\Models\MaterialEquipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class MaterialEquipmentBranchAccess
{
    public function forUser(User $user, ?MaterialEquipment $materialEquipment = null): Collection
    {
        $currentBranchId = $materialEquipment && $user->can('update', $materialEquipment)
            ? $materialEquipment->branch_id
            : null;

        $query = Branch::query()
            ->with('company:id,name')
            ->orderBy('name');

        match ($user->getRoleName()) {
            User::ADMIN_ROLE => null,
            'company_leader' => $query->where(function ($query) use ($user, $currentBranchId) {
                $query->whereHas(
                    'company.users',
                    fn ($users) => $users->where('users.id', $user->id)
                )->when(
                    $currentBranchId,
                    fn ($query) => $query->orWhere('branches.id', $currentBranchId)
                );
            }),
            'responsible_person' => $query->where(function ($query) use ($user, $currentBranchId) {
                $query->whereHas(
                    'users',
                    fn ($users) => $users->where('users.id', $user->id)
                )->when(
                    $currentBranchId,
                    fn ($query) => $query->orWhere('branches.id', $currentBranchId)
                );
            }),
            default => $query->whereRaw('1 = 0'),
        };

        $query->where(function ($query) use ($currentBranchId) {
            $query->where('visibility', '1');

            if ($currentBranchId) {
                $query->orWhere('branches.id', $currentBranchId);
            }
        });

        return $query->get();
    }

    public function idsForUser(User $user, ?MaterialEquipment $materialEquipment = null)
    {
        return $this->forUser($user, $materialEquipment)->pluck('id');
    }
}
