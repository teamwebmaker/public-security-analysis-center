<?php

namespace App\Http\Controllers;

use App\Presenters\TableRowDataPresenter;
use App\Presenters\TableHeaderDataPresenter;
use App\QueryBuilders\Sorts\LatestOccurrenceEndDateSort;
use App\QueryBuilders\Sorts\LatestOccurrenceStartDateSort;
use App\Models\TaskOccurrenceStatus;
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
        // Currently authenticated user
        $user = auth()->user();

        // Tasks assigned to this worker (pivot on task_workers)
        $taskQuery = $user->tasks();

        // Step 1: Get user's tasks and their statuses/services and make filtering and searching available
        $tasks = $this->buildTaskQuery($taskQuery)->paginate($this->perPage)
            ->appends(request()->query());

        // Step 2: Count tasks by latest occurrence status
        $statusCounts = [
            'pending' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'on_hold' => 0,
        ];

        foreach ($tasks as $task) {
            $statusName = $task->latestOccurrence?->status?->name;
            if (isset($statusCounts[$statusName])) {
                $statusCounts[$statusName]++;
            }
        }


        // Task table headers
        $taskHeaders = TableHeaderDataPresenter::workerTaskHeaders();

        // Filters for UI
        $statusOptions = TaskOccurrenceStatus::pluck('display_name', 'name')->toArray();
        $filters = [
            'status' => [
                'label' => 'სტატუსი',
                'options' => $statusOptions,
            ],
            'is_recurring' => [
                'label' => 'განმეორებადი',
                'options' => ['1' => 'დიახ', '0' => 'არა'],
            ],
        ];

        // Task table row data
        $taskTableRows = $tasks->map(fn($task) => TableRowDataPresenter::format($task, 'management_worker'));

        // Precompute actions & modal triggers per task (table expects arrays)
        $customActionMap = [];
        $modalTriggerMap = [];

        foreach ($tasks as $task) {
            $customActionMap[$task->id] = $this->customActionButtons($task);
            $modalTriggerMap[$task->id] = $this->modalTriggerButtons($task);
        }

        // Map human-readable column label same as header labels -> backend sort key
        $sortableMap = [
            'დაწყება' => 'latest_start_date',
            'დასრულება' => 'latest_end_date',
        ];

        return view("management.{$this->resourceName}.dashboard", [
            'tasks' => $tasks,
            'taskTableRows' => $taskTableRows,
            'taskHeaders' => $taskHeaders,
            'statusCounts' => $statusCounts,
            'sidebarItems' => config('sidebar.worker'),
            'customActionBtns' => fn($task) => $customActionMap[$task->id] ?? [],
            'modalTriggerBtns' => fn($task) => $modalTriggerMap[$task->id] ?? [],
            'sortableMap' => $sortableMap,
            'filters' => $filters,
        ]);
    }

    /**
     * Build the query for tasks.
     */
    protected function buildTaskQuery($query)
    {
        return QueryBuilder::for($query)
            ->allowedIncludes(['branch', 'service', 'latestOccurrence.status', 'latestOccurrence.workers'])
            ->allowedSorts([
                'branch_name_snapshot',
                'service_name_snapshot',
                AllowedSort::custom('latest_start_date', new LatestOccurrenceStartDateSort()),
                AllowedSort::custom('latest_end_date', new LatestOccurrenceEndDateSort()),
            ])->defaultSort('-created_at')
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $like = "%{$value}%";
                        $q->orWhereHas('branch', fn($b) => $b->where('name', 'LIKE', $like))
                            ->orWhereHas('service', fn($s) => $s->where('title->ka', 'LIKE', $like))
                            ->orWhereHas('latestOccurrence.status', fn($st) => $st->where('display_name', 'LIKE', $like));
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
            ->with(['branch', 'service', 'latestOccurrence.status', 'latestOccurrence.workers']);
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
