<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Presenters\TableRowDataPresenter;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ResponsiblePersonController extends Controller
{
    public $resourceName = 'responsible-person';
    // inProgressTasks
// userBranches
    public function displayDashboard()
    {
        $user = auth()->user(); // Currently authenticated user

        // Step 1: Get user's allowed service IDs 
        $allowedServiceIds = $user->services->pluck('id')->toArray();

        // Step 2: Load user's branches with tasks and their statuses/services
        $userBranches = $user->branches()->with([
            'tasks.status',
            'tasks.service',
        ])->get();

        // Step 3: Filter tasks
        $inProgressTasks = $userBranches->flatMap->tasks->filter(function ($task) use ($allowedServiceIds) {
            return $task->status &&
                $task->status->name === 'in_progress' &&
                in_array($task->service_id, $allowedServiceIds);
        });

        $sidebarItems = config('sidebar.responsible-person');

        $userTableRows = $inProgressTasks->map(fn($task) => TableRowDataPresenter::format($task, 'management'));
        $branchTableRows = $userBranches->map(fn($task) => TableRowDataPresenter::format($task, 'branches'));


        return view("management.{$this->resourceName}.dashboard", [
            'sidebarItems' => $sidebarItems,
            'userBranches' => $userBranches,
            'branchTableRows' => $branchTableRows,
            'inProgressTasks' => $inProgressTasks,
            'userTableRows' => $userTableRows
        ]);
    }

    public function displayTasks()
    {
        $user = auth()->user();

        // Step 1: Get allowed branch and service IDs
        $branchIds = $user->branches->pluck('id');
        $allowedServiceIds = $user->services->pluck('id'); // assuming `services()` relationship

        // Step 2: Build task query
        $tasks = QueryBuilder::for(Task::class)
            ->whereIn('branch_id', $branchIds)
            ->whereIn('service_id', $allowedServiceIds)
            ->allowedIncludes(['status', 'users', 'branch', 'service'])
            ->allowedSorts([
                'created_at',
                'branch_name',
                'service_name',
                'start_date',
                'updated_at',
            ])
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $q->orWhereHas('branch', fn($q) => $q->where('name', 'LIKE', "%$value%"))
                            ->orWhereHas('service', fn($q) => $q->where('title->ka', 'LIKE', "%$value%"))
                            ->orWhereHas('status', fn($q) => $q->where('display_name', 'LIKE', "%$value%"))
                            ->orWhereHas('users', fn($q) => $q->where('full_name', 'LIKE', "%$value%"));
                    });
                }),
            ])
            ->with(['status', 'users', 'branch', 'service'])
            ->paginate(10)
            ->appends(request()->query());


        $userTableRows = $tasks->map(fn($task) => TableRowDataPresenter::format($task, 'management'));

        return view("management.{$this->resourceName}.tasks", [
            'resourceName' => $this->resourceName,
            'tasks' => $tasks,
            'userTableRows' => $userTableRows,

        ]);
    }
}
