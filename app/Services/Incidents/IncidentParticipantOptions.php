<?php

namespace App\Services\Incidents;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Collection;

class IncidentParticipantOptions
{
    public function forBranches(Collection $branches): array
    {
        $branchIds = $branches->pluck('id');
        $result = $branches->mapWithKeys(fn(Branch $branch) => [$branch->id => []])->toArray();

        $users = User::query()
            ->where('is_active', true)
            ->where(function ($query) use ($branchIds) {
                $query->whereHas('branches', fn($branch) => $branch->whereIn('branches.id', $branchIds))
                    ->orWhereHas('companies.branches', fn($branch) => $branch->whereIn('branches.id', $branchIds));
            })
            ->with(['role:id,name', 'branches:id', 'companies.branches:id,company_id'])
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'role_id']);

        foreach ($branches as $branch) {
            $result[$branch->id] = $users
                ->filter(function (User $user) use ($branch) {
                    if ($user->getRoleName() === 'responsible_person') {
                        return $user->branches->contains('id', $branch->id);
                    }

                    return $user->getRoleName() === 'company_leader'
                        && $user->companies->flatMap->branches->contains('id', $branch->id);
                })
                ->map(fn(User $user) => [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'role' => $user->getRoleName(),
                    'role_label' => $user->getRoleName() === 'company_leader'
                        ? 'კომპანიის ხელმძღვანელი'
                        : 'პასუხისმგებელი პირი',
                ])
                ->values()
                ->all();
        }

        return $result;
    }
}
