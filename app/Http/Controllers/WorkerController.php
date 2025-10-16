<?php

namespace App\Http\Controllers;

use App\Presenters\TableRowDataPresenter;
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
        $tasks = QueryBuilder::for($user->tasks())
            ->allowedIncludes(['status', 'branch', 'service'])
            ->allowedSorts([
                'branch_name',
                'service_name',
                'start_date',
                'end_date',
            ])
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $q->orWhereHas('branch', fn($q) => $q->where('name', 'LIKE', "%$value%"))
                            ->orWhereHas('service', fn($q) => $q->where('title->ka', 'LIKE', "%$value%"))
                            ->orWhereHas('status', fn($q) => $q->where('display_name', 'LIKE', "%$value%"));
                    });
                }),
            ])
            ->with(['status', 'branch', 'service'])
            ->paginate($this->perPage)
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

        // Step 3: Prepare data for the view

        // Sidebar menu items
        $sidebarItems = config('sidebar.worker');

        // Task table row data
        $taskTableRows = $tasks->map(fn($task) => TableRowDataPresenter::format($task, 'management_worker'));

        // PUT: management/tasks/{task}
        $taskRoute = "management.{$this->resourceNameTasks}";

        // Buttons based on task status
        $customActionBtns = function ($task) use ($taskRoute) {
            $actions = [];

            if ($task->status->name === 'pending') {
                $actions[] = [
                    'label' => 'დაწყება',
                    'icon' => 'bi-play',
                    'route_name' => "{$taskRoute}.edit",
                    'method' => 'PUT',
                    'confirm' => 'ნამდვილად გსურთ სამუშაოს დაწყება?',
                    'class' => 'text-success',
                ];
            }

            return $actions;
        };


        $modalTriggerBtns = function ($task) use ($taskRoute) {
            $modalTriggers = [];
            if ($task->status->name === 'in_progress') {
                $modalTriggers[] = [
                    'label' => 'დასრულება',
                    'class' => '',
                    'icon' => 'bi-upload',
                    'modal_id' => 'uploadDocumentModal_' . $task->id,
                ];
            }
            return $modalTriggers;
        };

        return view("management.{$this->resourceName}.dashboard", [
            'tasks' => $tasks,
            'taskTableRows' => $taskTableRows,
            'inProgressTasks' => $inProgressTasks,
            'completedTasks' => $completedTasks,
            'pendingTasks' => $pendingTasks,
            'onHoldTasks' => $onHoldTasks,
            'sidebarItems' => $sidebarItems,
            'customActionBtns' => $customActionBtns,
            'modalTriggerBtns' => $modalTriggerBtns
        ]);
    }
}
