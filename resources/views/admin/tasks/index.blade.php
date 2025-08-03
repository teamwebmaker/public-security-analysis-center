@extends('layouts.admin.admin-dashboard')

@section('title', 'სამუშაოების სია')

@php
    $headers = ['#', 'სტატუსი', 'შემსრულებელი', 'საწყისი თარიღი', 'ფილიალი', 'სერვისი', 'ხილვადობა', 'შექმნის თარიღი', 'განახლების თარიღი'];

    $statusColors = [
        'pending' => 'warning',
        'in_progress' => 'info',
        'completed' => 'success',
        'on_hold' => 'secondary',
        'cancelled' => 'danger',
    ];

    /**
     * Generate a badge span with a Bootstrap background color
     */
    $makeBadge = fn(string $text, string $color) => '<span class="badge bg-' . e($color) . '">' . e($text) . '</span>';

    /**
     * Create a linked value or fallback to line-through text
     */
    $makeLinkOrFallback = function ($model, $routeName, $label, $fallback) {
        return $model
            ? '<a href="' . route($routeName, $model->id) . '" class="text-decoration-underline text-dark">' . e($label) . '</a>'
            : '<span style="text-decoration: line-through;">' . e($fallback) . '</span>';
    };

    $rows = $tasks->map(function ($task) use ($statusColors, $makeBadge, $makeLinkOrFallback) {
        $statusName = $task->status?->name ?? 'unknown';
        $statusColor = $statusColors[$statusName] ?? 'secondary';
        $statusLabel = $task->status?->display_name ?? 'უცნობი სტატუსი';

        return [
            'id' => $task->id,
            'status' => $makeBadge($statusLabel, $statusColor),
            'worker' => match (true) {
                $task->users->count() === 1 => '<select class="form-select form-select-sm" disabled> <option selected>' . e($task->users->first()->full_name) . '</option> </select>',
                $task->users->count() >= 2 => '<select class="form-select form-select-sm">' .
                '<option selected>სია...</option>' .  // One selected option before the list
                $task->users->map(
                    fn($user) =>
                    '<option disabled>' . e($user->full_name) . '</option>'
                )->implode('') .
                '</select>',
                default => $makeBadge('არ ჰყავს', 'danger')
            },

            'start_date' => e($task->start_date->diffForHumans()),
            'branch' => $makeLinkOrFallback($task->branch, 'branches.index', $task->branch?->name, $task->branch_name),
            'service' => $makeLinkOrFallback(
                $task->service,
                'services.index',
                $task->service?->title->ka ?? $task->service?->title->en ?? 'უცნობი',
                $task->service_name
            ),
            'visibility' => $makeBadge(
                $task->visibility ? 'ხილული' : 'დამალული',
                $task->visibility ? 'success' : 'danger'
            ),
            'created_at' => e($task->created_at->diffForHumans()),
            'updated_at' => e($task->updated_at->diffForHumans()),
        ];
    });

    $tooltipColumns = ['branch', 'service'];
@endphp

<x-admin.index-view :items="$tasks" :resourceName="$resourceName" containerClass="position-relative">
    <x-shared.table :items="$tasks" :resourceName="$resourceName" :headers="$headers" :rows="$rows" :actions="true"
        :tooltipColumns="$tooltipColumns" />
</x-admin.index-view>