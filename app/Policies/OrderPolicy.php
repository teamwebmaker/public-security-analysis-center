<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    private const ALLOWED_ROLES = [
        User::ADMIN_ROLE,
        'worker',
        'company_leader',
        'responsible_person',
    ];

    public function viewAny(User $user): bool
    {
        return in_array($user->getRoleName(), self::ALLOWED_ROLES, true);
    }

    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin()
            || (int) $order->created_by_user_id === (int) $user->id
            || $order->userParticipants()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return in_array($user->getRoleName(), self::ALLOWED_ROLES, true);
    }

    public function update(User $user, Order $order): bool
    {
        return $user->isAdmin()
            || (int) $order->created_by_user_id === (int) $user->id;
    }

    public function sign(User $user, Order $order): bool
    {
        return in_array($user->getRoleName(), ['company_leader', 'responsible_person'], true)
            && $order->userParticipants()->where('user_id', $user->id)->exists();
    }
}
