<?php

namespace App\Policies;

use App\Models\Instruction;
use App\Models\User;

class InstructionPolicy
{
    public function view(User $user, Instruction $instruction): bool
    {
        return $user->isAdmin()
            || (
                $user->getRoleName() === 'worker'
                && (string) $instruction->visibility === '1'
                && $instruction->users()->where('users.id', $user->id)->exists()
            );
    }
}
