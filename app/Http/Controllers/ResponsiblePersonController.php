<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskOccurrenceStatus;
use App\Presenters\TableHeaderDataPresenter;
use App\Presenters\TableRowDataPresenter;
use App\QueryBuilders\Sorts\LatestOccurrenceDueDateSort;
use App\QueryBuilders\Sorts\LatestOccurrenceEndDateSort;
use App\QueryBuilders\Sorts\LatestOccurrenceStartDateSort;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ResponsiblePersonController extends Controller
{
    public $resourceName = 'responsible-person';

    public function displayDashboard()
    {
        $user = auth()->user();
        $allowedServiceIds = $this->allowedServiceIds($user);
        $branchIds = $this->branchIds($user);

        $userBranches = $user->branches()
            ->with('company')
            ->paginate(5, ['*'], 'branches_page')
            ->appends(request()->query());

        $inProgressTasks = $this->buildInProgressTasksQuery($branchIds, $allowedServiceIds)
            ->paginate(5, ['*'], 'tasks_page')
            ->appends(request()->query());

        $taskHeaders = array_values(array_filter(
            TableHeaderDataPresenter::responsiblePersonTaskHeaders(),
            fn($header) => $header !== 'სამუშაო დასრულდა'
        ));

        $taskRows = $inProgressTasks->getCollection()
            ->map(fn($task) => TableRowDataPresenter::format($task, 'management'))
            ->map(function ($row) {
                unset($row['end_date']);
                return $row;
            });

        $branchTableHeaders = TableHeaderDataPresenter::responsiblePersonBranchHeaders();
        $branchTableRows = $userBranches->getCollection()
            ->map(fn($task) => TableRowDataPresenter::format($task, 'branches'));

        $sidebarItems = config('sidebar.responsible-person');

        return view("management.{$this->resourceName}.dashboard", [
            'sidebarItems' => $sidebarItems,
            'userBranches' => $userBranches,
            'taskHeaders' => $taskHeaders,
            'taskRows' => $taskRows,
            'branchTableRows' => $branchTableRows,
            'branchTableHeaders' => $branchTableHeaders,
            'inProgressTasks' => $inProgressTasks,
            'sortableMap' => $this->dashboardSortableMap(),
        ]);
    }

    public function displayTasks()
    {
        $user = auth()->user();
        $branchIds = $this->branchIds($user);
        $allowedServiceIds = $this->allowedServiceIds($user);

        $tasks = $this->buildTasksQuery($branchIds, $allowedServiceIds)
            ->paginate(10)
            ->appends(request()->query());

        $userTableRows = $tasks->map(fn($task) => TableRowDataPresenter::format($task, 'management'));
        $taskHeaders = TableHeaderDataPresenter::responsiblePersonTaskHeaders();

        $sidebarItems = config('sidebar.responsible-person');

        return view("management.{$this->resourceName}.tasks", [
            'resourceName' => $this->resourceName,
            'tasks' => $tasks,
            'userTableRows' => $userTableRows,
            'sidebarItems' => $sidebarItems,
            'filters' => $this->taskFilters(),
            'sortableMap' => $this->tasksSortableMap(),
            'taskHeaders' => $taskHeaders,
        ]);
    }

    protected function branchIds($user)
    {
        return $user->branches->pluck('id');
    }

    protected function allowedServiceIds($user): array
    {
        return $user->services->pluck('id')->toArray();
    }

    protected function buildInProgressTasksQuery($branchIds, array $allowedServiceIds)
    {
        return QueryBuilder::for(Task::class)
            ->whereIn('branch_id', $branchIds)
            ->whereIn('service_id', $allowedServiceIds)
            ->whereHas('latestOccurrence.status', fn($q) => $q->where('name', 'in_progress'))
            ->with(['users', 'branch', 'service', 'latestOccurrence.status'])
            ->allowedSorts([
                AllowedSort::custom('latest_start_date', new LatestOccurrenceStartDateSort()),
                AllowedSort::custom('latest_due_date', new LatestOccurrenceDueDateSort()),
            ]);
    }

    protected function buildTasksQuery($branchIds, array $allowedServiceIds)
    {
        return QueryBuilder::for(Task::class)
            ->whereIn('branch_id', $branchIds)
            ->whereIn('service_id', $allowedServiceIds)
            ->allowedIncludes(['users', 'branch', 'service', 'latestOccurrence.status'])
            ->allowedSorts([
                AllowedSort::custom('latest_due_date', new LatestOccurrenceDueDateSort()),
                AllowedSort::custom('latest_start_date', new LatestOccurrenceStartDateSort()),
                AllowedSort::custom('latest_end_date', new LatestOccurrenceEndDateSort()),
            ])
            ->allowedFilters($this->taskFiltersConfig())
            ->with(['users', 'branch', 'service', 'latestOccurrence.status']);
    }

    protected function taskFilters(): array
    {
        $statusOptions = TaskOccurrenceStatus::pluck('display_name', 'name')->toArray();

        return [
            'status' => [
                'label' => 'სტატუსი',
                'options' => $statusOptions,
            ],
            'is_recurring' => [
                'label' => 'განმეორებადი',
                'options' => ['1' => 'დიახ', '0' => 'არა'],
            ],
        ];
    }

    protected function taskFiltersConfig(): array
    {
        return [
            AllowedFilter::callback('search', function ($query, $value) {
                $query->where(function ($q) use ($value) {
                    $q->orWhereHas('branch', fn($q) => $q->where('name', 'LIKE', "%$value%"))
                        ->orWhereHas('service', fn($q) => $q->where('title->ka', 'LIKE', "%$value%"))
                        ->orWhereHas('latestOccurrence.status', fn($q) => $q->where('display_name', 'LIKE', "%$value%"))
                        ->orWhereHas('users', fn($q) => $q->where('full_name', 'LIKE', "%$value%"));
                });
            }),
            AllowedFilter::callback('status', function ($query, $value) {
                $query->whereHas('latestOccurrence.status', function ($q) use ($value) {
                    $q->where('name', $value)->orWhere('display_name', $value);
                });
            }),
            AllowedFilter::exact('is_recurring'),
        ];
    }

    protected function dashboardSortableMap(): array
    {
        return [
            'განმეორების თარიღი' => 'latest_due_date',
            'სამუშაო დაიწყო' => 'latest_start_date',
        ];
    }

    protected function tasksSortableMap(): array
    {
        return [
            'განმეორების თარიღი' => 'latest_due_date',
            'სამუშაო დაიწყო' => 'latest_start_date',
            'სამუშაო დასრულდა' => 'latest_end_date',
        ];
    }
}
