@props(['branch'])
<div class="d-flex flex-column gap-2">
    <x-management.branch.task-status icon="bi bi-clock" color="warning" :count="$branch->pending_tasks_count"
        label="მოლოდინში" />

    <x-management.branch.task-status icon="bi bi-hourglass-split" color="info" :count="$branch->active_tasks_count"
        label="პროცესშია" />

    <x-management.branch.task-status icon="bi bi-check2-circle" color="success" :count="$branch->completed_tasks_count"
        label="დასრულებული" />
</div>