<?php

namespace App\Services\Tasks;

use App\Models\Branch;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;

class TaskFilterOptionsService
{
    /**
     * Build company, branch, and service filter options strictly from the
     * caller's already-authorized task scope.
     */
    public function forTaskScope(Builder $taskScope): array
    {
        $branchIds = (clone $taskScope)
            ->whereNotNull('branch_id')
            ->distinct()
            ->pluck('branch_id')
            ->map(fn($id) => (int) $id)
            ->all();

        $serviceIds = (clone $taskScope)
            ->whereNotNull('service_id')
            ->distinct()
            ->pluck('service_id')
            ->map(fn($id) => (int) $id)
            ->all();

        $branches = Branch::query()
            ->whereIn('id', $branchIds)
            ->with('company:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'company_id']);

        $companies = $branches
            ->pluck('company')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $branchOptions = $branches
            ->pluck('name', 'id')
            ->toArray();

        $serviceOptions = Service::query()
            ->whereIn('id', $serviceIds)
            ->get()
            ->sortBy(fn(Service $service) => $service->title->ka ?? $service->title->en ?? '')
            ->mapWithKeys(fn(Service $service) => [
                $service->id => $service->title->ka ?? $service->title->en ?? "Service #{$service->id}",
            ])
            ->toArray();

        return [
            'company_id' => [
                'label' => 'კომპანია',
                'options' => $companies,
            ],
            'branch_id' => [
                'label' => 'ფილიალი',
                'options' => $branchOptions,
            ],
            'service_id' => [
                'label' => 'სერვისი',
                'options' => $serviceOptions,
            ],
        ];
    }
}
