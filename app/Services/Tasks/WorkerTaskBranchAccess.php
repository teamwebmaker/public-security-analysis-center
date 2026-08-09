<?php

namespace App\Services\Tasks;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Collection;

class WorkerTaskBranchAccess
{
    public function branchIdsFor(User $worker): Collection
    {
        return $this->queryFor($worker)->pluck('branches.id');
    }

    public function optionsFor(User $worker): array
    {
        return $this->queryFor($worker)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    private function queryFor(User $worker)
    {
        return Branch::query()
            ->where('visibility', '1')
            ->whereIn('company_id', function ($query) use ($worker) {
                $query->select('company_id')
                    ->from('worker_companies')
                    ->where('user_id', $worker->id);
            });
    }
}
