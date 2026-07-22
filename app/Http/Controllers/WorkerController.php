<?php

namespace App\Http\Controllers;

use App\Presenters\TableRowDataPresenter;
use App\Presenters\TableHeaderDataPresenter;
use App\QueryBuilders\Sorts\LatestOccurrenceEndDateSort;
use App\QueryBuilders\Sorts\LatestOccurrenceStartDateSort;
use App\Models\Task;
use App\Models\TaskOccurrence;
use App\Models\TaskOccurrenceStatus;
use App\Models\TaskWorkerInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class WorkerController extends Controller
{
    public $resourceName = 'worker';
    public $resourceNameTasks = 'tasks';
    public int $perPage = 10;

    public function displayDashboard()
    {
        $workerId = (int) auth()->id();
        $assignedTasks = Task::query()->whereHas('users', function ($query) use ($workerId) {
            $query->where('users.id', $workerId);
        });

        $statusCounts = collect(['pending', 'in_progress', 'completed', 'on_hold'])
            ->mapWithKeys(function (string $status) use ($assignedTasks) {
                return [
                    $status => (clone $assignedTasks)
                        ->whereHas('latestOccurrence.status', fn($query) => $query->where('name', $status))
                        ->count(),
                ];
            })
            ->toArray();

        $pendingInvitationsCount = TaskWorkerInvitation::query()
            ->where('invited_worker_id', $workerId)
            ->where('status', TaskWorkerInvitation::STATUS_PENDING)
            ->whereHas('task', fn($query) => $query->active())
            ->count();

        return view("management.{$this->resourceName}.dashboard", [
            'statusCounts' => $statusCounts,
            'pendingInvitationsCount' => $pendingInvitationsCount,
            'sidebarItems' => config('sidebar.worker'),
        ]);
    }

    public function displayTasks()
    {
        $this->authorize('viewActiveTasks', Task::class);

        $workerId = (int) auth()->id();
        $myTasksQuery = Task::query()
            ->active()
            ->whereHas('users', function ($query) use ($workerId) {
                $query->where('users.id', $workerId);
            });

        $tasks = $this->buildTaskQuery($myTasksQuery)
            ->paginate($this->perPage)
            ->appends(request()->query());

        $taskRows = $tasks->map(fn(Task $task) => TableRowDataPresenter::workerTaskRow($task));

        $pendingInvitations = TaskWorkerInvitation::query()
            ->where('invited_worker_id', $workerId)
            ->where('status', TaskWorkerInvitation::STATUS_PENDING)
            ->whereHas('task', fn($query) => $query->active())
            ->with([
                'inviter:id,full_name',
                'task:id,branch_id,service_id,branch_name_snapshot,service_name_snapshot',
                'task.branch:id,name',
                'task.service:id,title',
            ])
            ->latest()
            ->get();

        $inviteableWorkers = User::query()
            ->where('id', '!=', $workerId)
            ->where('is_active', true)
            ->whereHas('role', fn($query) => $query->where('name', 'worker'))
            ->orderBy('full_name')
            ->get(['id', 'full_name']);

        return view('management.worker.tasks.index', [
            'tasks' => $tasks,
            'taskRows' => $taskRows,
            'pendingInvitations' => $pendingInvitations,
            'inviteableWorkers' => $inviteableWorkers,
            'taskHeaders' => TableHeaderDataPresenter::workerTaskHeaders(),
            'sidebarItems' => config('sidebar.worker'),
            'filters' => [
                'status' => [
                    'label' => 'სტატუსი',
                    'options' => TaskOccurrenceStatus::query()
                        ->whereIn('name', Task::ACTIVE_OCCURRENCE_STATUSES)
                        ->pluck('display_name', 'name')
                        ->toArray(),
                ],
                'is_recurring' => [
                    'label' => 'განმეორებადი',
                    'options' => ['1' => 'დიახ', '0' => 'არა'],
                ],
            ],
            'sortableMap' => [
                'დაწყება' => 'latest_start_date',
                'დასრულება' => 'latest_end_date',
            ],
            'taskActions' => fn(Task $task) => $this->customActionButtons($task),
            'taskModalTriggers' => fn(Task $task) => array_merge(
                $this->modalTriggerButtons($task),
                $this->invitationModalTriggerButtons($task, $workerId)
            ),
        ]);
    }

    /**
     * Build the query for tasks.
     */
    protected function buildTaskQuery($query)
    {
        $latestOccurrenceCreatedAt = $this->latestOccurrenceTimestampSubquery('created_at');

        return QueryBuilder::for($query)
            ->allowedIncludes(['users', 'branch', 'service', 'latestOccurrence.status', 'latestOccurrence.workers'])
            ->allowedSorts([
                'branch_name_snapshot',
                'service_name_snapshot',
                AllowedSort::custom('latest_start_date', new LatestOccurrenceStartDateSort()),
                AllowedSort::custom('latest_end_date', new LatestOccurrenceEndDateSort()),
            ])->defaultSort('-created_at')
            ->orderByDesc($latestOccurrenceCreatedAt)
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $value = is_array($value) ? $value[0] : $value;
                    $value = trim((string) $value);
                    $occurrenceId = ctype_digit($value) ? (int) $value : null;

                    $query->where(function ($q) use ($value, $occurrenceId) {
                        $like = "%{$value}%";
                        $q->where('service_name_snapshot', 'LIKE', $like)
                            ->orWhereHas('branch', fn($b) => $b->where('name', 'LIKE', $like))
                            ->orWhereHas('service', fn($s) => $s->where('title->ka', 'LIKE', $like))
                            ->orWhereHas('latestOccurrence.status', fn($st) => $st->where('display_name', 'LIKE', $like));

                        if ($occurrenceId !== null) {
                            $q->orWhereHas('taskOccurrences', function ($q) use ($occurrenceId) {
                                $q->where('id', $occurrenceId);
                            });
                        }
                    });
                }),
                AllowedFilter::callback('status', function ($query, $value) {
                    $query->whereHas('latestOccurrence.status', function ($q) use ($value) {
                        $q->where('name', $value)
                            ->orWhere('display_name', $value);
                    });
                }),
                AllowedFilter::exact('is_recurring'),
            ])
            ->with([
                'users',
                'branch',
                'service',
                'latestOccurrence.status',
                'latestOccurrence.workers',
                'workerInvitations',
            ]);
    }

    /**
     * Latest occurrence timestamp subquery for ordering tasks based on their latest occurrence timestamp.
     */
    protected function latestOccurrenceTimestampSubquery(string $column = 'created_at')
    {
        return TaskOccurrence::select($column)
            ->whereColumn('task_occurrences.task_id', 'tasks.id')
            ->latest()
            ->limit(1);
    }

    /**
     * Custom action buttons depending on task status.
     */
    protected function customActionButtons($task = null): array
    {
        if (!$task) {
            return [];
        }

        $taskRoute = "management.{$this->resourceNameTasks}";
        $actions = [];

        // Only show start button if latest occurrence is pending
        if ($task->latestOccurrence?->status?->name === 'pending') {
            $actions[] = [
                'label' => 'დაწყება',
                'icon' => 'bi-play',
                'route_name' => "{$taskRoute}.edit",
                'method' => 'PUT',
                'confirm' => 'ნამდვილად გსურთ სამუშაოს დაწყება?',
                'class' => 'text-success',
            ];
        }

        // If in progress and not requires document, show upload action (alternative to modal)
        if ($task->latestOccurrence?->status?->name === 'in_progress' && $task->latestOccurrence?->requires_document === false) {
            $actions[] = [
                'label' => 'დასრულება',
                'icon' => 'bi-check2',
                'route_name' => "{$taskRoute}.upload-document",
                'method' => 'PUT',
                'confirm' => 'ნამდვილად გსურთ სამუშაოს დასრულება?',
                'class' => 'text-primary',
            ];
        }

        return $actions;
    }

    /**
     * Modal trigger buttons depending on task status.
     */
    protected function modalTriggerButtons($task = null): array
    {
        if (!$task) {
            return [];
        }

        // Only show upload document modal if task is in progress and requires a document
        if ($task->latestOccurrence?->status?->name !== 'in_progress') {
            return [];
        }
        if ($task->latestOccurrence?->requires_document === false) {
            return [];
        }

        return [
            [
                'label' => 'დასრულება',
                'icon' => 'bi-upload',
                'modal_id' => "uploadDocumentModal_{$task->id}",
                'class' => '',
            ]
        ];

    }

    protected function invitationModalTriggerButtons(Task $task, int $workerId): array
    {
        if ((int) $task->created_by_user_id !== $workerId) {
            return [];
        }

        return [
            [
                'label' => 'კოლეგის მოწვევა',
                'icon' => 'bi-person-plus',
                'modal_id' => "taskInvitationModal_{$task->id}",
                'class' => 'btn-outline-secondary',
            ],
        ];
    }

    public function displayInstructions()
    {
        return $this->renderPaginatedView('instructions', 'instructions.index');

    }

    public function displayDocumentTemplates()
    {
        return $this->renderPaginatedView('document_templates', 'document-templates.index');
    }

    protected function renderPaginatedView(string $relation, string $viewPath)
    {
        $user = Auth::user();

        return view("management.{$this->resourceName}.{$viewPath}", [
            'sidebarItems' => config('sidebar.worker'),
            'resourceName' => str_replace('_', '-', $relation),
            $relation => $user->{$relation}()->paginate($this->perPage),
        ]);
    }
}
