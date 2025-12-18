<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskOccurrence;
use App\Models\TaskOccurrenceStatus;
use App\Presenters\TableRowDataPresenter;
use App\Presenters\TableHeaderDataPresenter;
use App\QueryBuilders\Sorts\LatestOccurrenceEndDateSort;
use App\QueryBuilders\Sorts\LatestOccurrenceStartDateSort;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class CompanyLeaderController extends Controller
{
    public $resourceName = 'company-leader';

    public function displayDashboard()
    {
        $user = auth()->user(); // Currently authenticated user

        // Companies related to the user, eager loading branches, tasks (with status), and users
        $userCompanies = $user->companies()->with([
            'branches.tasks.latestOccurrence.status',
            'branches.users',
        ])->get();

        // Update each branch inside companies with task counts
        $userCompanies->each(function ($company) {
            $company->branches->transform(function ($branch) {
                $pending = $branch->tasks->filter(fn($t) => $t->latestOccurrence?->status?->name === 'pending')->count();
                $active = $branch->tasks->filter(fn($t) => $t->latestOccurrence?->status?->name === 'in_progress')->count();
                $completed = $branch->tasks->filter(fn($t) => $t->latestOccurrence?->status?->name === 'completed')->count();

                $branch->pending_tasks_count = $pending;
                $branch->active_tasks_count = $active;
                $branch->completed_tasks_count = $completed;

                return $branch;
            });
        });

        // Collect all branches across companies
        $allBranches = $userCompanies->pluck('branches')->flatten();

        $branchIds = $user->companies->flatMap->branches->pluck('id');

        $latestOccurrenceUpdatedAt = $this->latestOccurrenceTimestampSubquery();

        $tasks = QueryBuilder::for(Task::class)
            ->whereIn('branch_id', $branchIds)
            ->allowedIncludes(['users', 'branch', 'service', 'latestOccurrence.status'])
            ->allowedSorts([
                'created_at',
                'updated_at',
                AllowedSort::custom('latest_start_date', new LatestOccurrenceStartDateSort()),
                AllowedSort::custom('latest_end_date', new LatestOccurrenceEndDateSort()),
            ])
            ->with(['branch', 'service', 'latestOccurrence.status'])
            ->orderByDesc($latestOccurrenceUpdatedAt)
            ->orderByDesc('updated_at') // fallback
            ->limit(5)
            ->get();

        // In-progress tasks
        $inProgressTasks = $allBranches
            ->pluck('tasks')
            ->flatten()
            ->filter(fn($task) => $task->latestOccurrence?->status?->name === 'in_progress');

        $userBranchMap = [];

        // Loop through all branches and map each user to their corresponding branches (to get responsible person names)
        foreach ($allBranches as $branch) {
            foreach ($branch->users as $user) {
                $userId = $user->id;

                if (!isset($userBranchMap[$userId])) {
                    $userBranchMap[$userId] = [
                        'user' => $user,
                        'branches' => [],
                    ];
                }

                $userBranchMap[$userId]['branches'][] = $branch->name;
            }
        }

        // Count responsible persons
        $uniqueUserCount = count($userBranchMap);

        // Sidebar menu items
        $sidebarItems = config('sidebar.company-leader');

        $sortableMap = $this->taskSortableMap();

        $taskHeaders = TableHeaderDataPresenter::companyLeaderTaskHeaders();
        $taskRows = $tasks->map(fn($task) => TableRowDataPresenter::format($task, 'management'));


        return view("management.{$this->resourceName}.dashboard", [
            'tasks' => $tasks,
            'inProgressTasks' => $inProgressTasks,

            'userCompanies' => $userCompanies,
            'userBranchMap' => $userBranchMap,
            'branchUsersCount' => $uniqueUserCount,

            'taskHeaders' => $taskHeaders,
            'taskRows' => $taskRows,

            'sidebarItems' => $sidebarItems,
            'sortableMap' => $sortableMap,

        ]);

    }

    public function displayTasks()
    {
        $user = auth()->user();

        $branchIds = $user->companies->flatMap->branches->pluck('id');

        $latestOccurrenceUpdatedAt = $this->latestOccurrenceTimestampSubquery();

        $tasks = QueryBuilder::for(Task::class)
            ->whereIn('branch_id', $branchIds)
            ->allowedIncludes(["users", "branch", "service", "latestOccurrence.status"])
            // Allowed sorting fields
            ->allowedSorts([
                "branch_name_snapshot",
                "service_name_snapshot",
                AllowedSort::custom('latest_start_date', new LatestOccurrenceStartDateSort()),
                AllowedSort::custom('latest_end_date', new LatestOccurrenceEndDateSort()),
                "created_at",
            ])
            // Allowed Search fields
            ->allowedFilters([
                AllowedFilter::callback("search", function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $q->orWhereHas(
                            "branch",
                            fn($q) => $q->where("name", "LIKE", "%$value%")
                        )
                            ->orWhereHas(
                                "service",
                                fn($q) => $q->where(
                                    "title->ka",
                                    "LIKE",
                                    "%$value%"
                                )
                            )
                            ->orWhereHas(
                                "latestOccurrence.status",
                                fn($q) => $q->where(
                                    "display_name",
                                    "LIKE",
                                    "%$value%"
                                )->orWhere('name', 'LIKE', "%$value%")
                            )
                            ->orWhereHas(
                                "users",
                                fn($q) => $q->where(
                                    "full_name",
                                    "LIKE",
                                    "%$value%"
                                )
                            );
                    });
                }),
                AllowedFilter::callback('status', function ($query, $value) {
                    $query->whereHas('latestOccurrence.status', function ($q) use ($value) {
                        $q->where('name', $value)->orWhere('display_name', $value);
                    });
                }),
                AllowedFilter::exact('is_recurring'),
            ])
            ->with(["users", "branch", "service", "latestOccurrence.status"])
            ->orderByDesc($latestOccurrenceUpdatedAt)
            ->paginate(10)
            ->appends(request()->query());

        $taskHeaders = TableHeaderDataPresenter::companyLeaderTaskHeaders();
        $taskRows = $tasks->map(fn($task) => TableRowDataPresenter::format($task, 'management'));

        // Sidebar menu items
        $sidebarItems = config('sidebar.company-leader');

        // Sorting & filtering
        $sortableMap = $this->taskSortableMap();
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
        return view("management.{$this->resourceName}.tasks", [
            'resourceName' => $this->resourceName,
            'tasks' => $tasks,
            'taskRows' => $taskRows,
            'taskHeaders' => $taskHeaders,
            'sidebarItems' => $sidebarItems,
            'sortableMap' => $sortableMap,
            'filters' => $filters
        ]);
    }

    protected function taskSortableMap(): array
    {
        return [
            'დაწყება' => 'latest_start_date',
            'დასრულება' => 'latest_end_date',
        ];
    }

    /**
     * Latest occurrence timestamp subquery for ordering tasks.
     */
    protected function latestOccurrenceTimestampSubquery(string $column = 'updated_at')
    {
        return TaskOccurrence::select($column)
            ->whereColumn('task_occurrences.task_id', 'tasks.id')
            ->latest()
            ->limit(1);
    }
}
