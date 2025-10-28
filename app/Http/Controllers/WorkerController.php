<?php

namespace App\Http\Controllers;

use App\Presenters\TableRowDataPresenter;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
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

        // Step 1: Get user's tasks and their statuses/services and make filtering and searching available
        $tasks = $this->buildTaskQuery($user->tasks())->paginate($this->perPage)
            ->appends(request()->query());

        // Step 2: Filter tasks by status
        $pendingTasks = $tasks->filter(function ($task) {
            return $task->status && $task->status->name === 'pending';
        });

        $inProgressTasks = $tasks->filter(function ($task) {
            return $task->status && $task->status->name === 'in_progress';
        });

        $completedTasks = $tasks->filter(function ($task) {
            return $task->status && $task->status->name === 'completed';
        });

        $onHoldTasks = $tasks->filter(function ($task) {
            return $task->status && $task->status->name === 'on_hold';
        });


        // Task table row data
        $taskTableRows = $tasks->map(fn($task) => TableRowDataPresenter::format($task, 'management_worker'));


        return view("management.{$this->resourceName}.dashboard", [
            'tasks' => $tasks,
            'taskTableRows' => $taskTableRows,
            'inProgressTasks' => $inProgressTasks,
            'completedTasks' => $completedTasks,
            'pendingTasks' => $pendingTasks,
            'onHoldTasks' => $onHoldTasks,
            'sidebarItems' => config('sidebar.worker'),
            'customActionBtns' => $this->customActionButtons(),
            'modalTriggerBtns' => $this->modalTriggerButtons(),
        ]);
    }

    /**
     * Build the query for tasks.
     */
    protected function buildTaskQuery($query)
    {
        return QueryBuilder::for($query)
            ->allowedIncludes(['status', 'branch', 'service'])
            ->allowedSorts(['branch_name', 'service_name', 'start_date', 'end_date'])
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $like = "%{$value}%";
                        $q->orWhereHas('branch', fn($b) => $b->where('name', 'LIKE', $like))
                            ->orWhereHas('service', fn($s) => $s->where('title->ka', 'LIKE', $like))
                            ->orWhereHas('status', fn($st) => $st->where('display_name', 'LIKE', $like));
                    });
                }),
            ])
            ->with(['status', 'branch', 'service']);
    }

    /**
     * Custom action buttons depending on task status.
     */
    protected function customActionButtons()
    {
        $taskRoute = "management.{$this->resourceNameTasks}";

        return function ($task) use ($taskRoute) {
            if ($task->status?->name !== 'pending') {
                return [];
            }

            return [
                [
                    'label' => 'დაწყება',
                    'icon' => 'bi-play',
                    'route_name' => "{$taskRoute}.edit",
                    'method' => 'PUT',
                    'confirm' => 'ნამდვილად გსურთ სამუშაოს დაწყება?',
                    'class' => 'text-success',
                ]
            ];
        };
    }

    /**
     * Modal trigger buttons depending on task status.
     */
    protected function modalTriggerButtons()
    {
        return function ($task) {
            if ($task->status?->name !== 'in_progress') {
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
        };

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
