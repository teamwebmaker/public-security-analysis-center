<?php

namespace App\Support;

use App\Models\Task;

class TableHelper
{
    public static function formatTaskRow(Task $task): array
    {
        $badge = fn($text, $color) => '<span class="badge bg-' . e($color) . '">' . e($text) . '</span>';
        $link = fn($model, $route, $label, $fallback) =>
            $model
                ? '<a href="' . route($route, $model->id) . '" class="text-decoration-underline text-dark">' . e($label) . '</a>'
                : '<span class="text-decoration-line-through">' . e($fallback) . '</span>';

        $statusColor = [
            'pending' => 'warning',
            'in_progress' => 'info',
            'completed' => 'success',
            'on_hold' => 'secondary',
            'cancelled' => 'danger',
        ][$task->status?->name] ?? 'secondary';

        return [
            'id' => $task->id,
            'status' => $badge($task->status?->display_name ?? 'უცნობი', $statusColor),
            'worker' => $task->users->count() === 1
                ? '<select class="form-select form-select-sm" disabled><option>' . e($task->users->first()->full_name) . '</option></select>'
                : ($task->users->count() >= 2
                    ? '<select class="form-select form-select-sm"><option selected>სია...</option>' .
                        $task->users->map(fn($u) => '<option disabled>' . e($u->full_name) . '</option>')->implode('') .
                      '</select>'
                    : $badge('არ ჰყავს', 'danger')),
            'branch' => $link($task->branch, 'branches.index', $task->branch?->name, $task->branch_name),
            'service' => $link(
                $task->service,
                'services.index',
                $task->service?->title->ka ?? $task->service?->title->en ?? 'უცნობი',
                $task->service_name
            ),
            'visibility' => $badge($task->visibility ? 'ხილული' : 'დამალული', $task->visibility ? 'success' : 'danger'),
            'start_date' => e($task->start_date->diffForHumans()),
            'created_at' => e($task->created_at->diffForHumans()),
            'updated_at' => e($task->updated_at->diffForHumans()),
        ];
        
    }
}
