<?php

namespace App\Policies;

use App\Models\MaterialEquipment;
use App\Models\User;

class MaterialEquipmentPolicy
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

    public function view(User $user, MaterialEquipment $materialEquipment): bool
    {
        return MaterialEquipment::query()
            ->visibleTo($user)
            ->whereKey($materialEquipment->getKey())
            ->exists();
    }

    public function create(User $user): bool
    {
        return in_array($user->getRoleName(), self::CREATE_ROLES, true);
    }

    public function update(User $user, MaterialEquipment $materialEquipment): bool
    {
        return $user->isAdmin()
            || (int) $materialEquipment->created_by_user_id === (int) $user->id;
    }

    public function delete(User $user, MaterialEquipment $materialEquipment): bool
    {
        return $this->update($user, $materialEquipment);
    }
}
