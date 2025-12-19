<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskOccurrence;
use App\Models\TaskOccurrenceStatus;
use App\Presenters\TableRowDataPresenter;
use App\Presenters\TableHeaderDataPresenter;
use App\QueryBuilders\Sorts\LatestOccurrenceDueDateSort;
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
        $user = auth()->user();
        $userCompanies = $this->loadCompaniesWithBranches($user);
        $allBranches = $this->collectBranches($userCompanies);

        $this->hydrateBranchTaskCounts($userCompanies);

        $tasks = $this->buildLatestTasksQuery($this->branchIds($user))->limit(5)->get();
        $inProgressTasks = $this->filterInProgressTasks($allBranches);

        $userBranchMap = $this->buildUserBranchMap($allBranches);
        $uniqueUserCount = count($userBranchMap);

        $taskHeaders = TableHeaderDataPresenter::companyLeaderTaskHeaders();
        $taskRows = $this->taskRows($tasks);

        $sidebarItems = config('sidebar.company-leader');

        return view("management.{$this->resourceName}.dashboard", [
            'tasks' => $tasks,
            'inProgressTasks' => $inProgressTasks,

            'userCompanies' => $userCompanies,
            'userBranchMap' => $userBranchMap,
            'branchUsersCount' => $uniqueUserCount,

            'taskHeaders' => $taskHeaders,
            'taskRows' => $taskRows,

            'sidebarItems' => $sidebarItems,
            'sortableMap' => $this->taskSortableMap(),

        ]);

    }

    public function displayTasks()
    {
        $user = auth()->user();

        $tasks = $this->buildTasksQuery($this->branchIds($user))
            ->paginate(10)
            ->appends(request()->query());

        $taskHeaders = TableHeaderDataPresenter::companyLeaderTaskHeaders();
        $taskRows = $this->taskRows($tasks);

        $sidebarItems = config('sidebar.company-leader');

        return view("management.{$this->resourceName}.tasks", [
            'resourceName' => $this->resourceName,
            'tasks' => $tasks,
            'taskRows' => $taskRows,
            'taskHeaders' => $taskHeaders,
            'sidebarItems' => $sidebarItems,
            'sortableMap' => $this->taskSortableMap(),
            'filters' => $this->taskFilters(),
        ]);
    }


    protected function taskRows($tasks)
    {
        return $tasks->map(fn($task) => TableRowDataPresenter::format($task, 'management'));
    }

    protected function branchIds($user)
    {
        return $user->companies->flatMap->branches->pluck('id');
    }

    protected function loadCompaniesWithBranches($user)
    {
        return $user->companies()->with([
            'branches.tasks.latestOccurrence.status',
            'branches.users',
        ])->get();
    }

    protected function collectBranches($userCompanies)
    {
        return $userCompanies->pluck('branches')->flatten();
    }

    protected function filterInProgressTasks($branches)
    {
        return $branches
            ->pluck('tasks')
            ->flatten()
            ->filter(fn($task) => $task->latestOccurrence?->status?->name === 'in_progress');
    }

    protected function buildUserBranchMap($branches): array
    {
        $userBranchMap = [];

        foreach ($branches as $branch) {
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

        return $userBranchMap;
    }

    protected function hydrateBranchTaskCounts($userCompanies): void
    {
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
    }

    protected function buildLatestTasksQuery($branchIds)
    {
        $latestOccurrenceUpdatedAt = $this->latestOccurrenceTimestampSubquery();

        return QueryBuilder::for(Task::class)
            ->whereIn('branch_id', $branchIds)
            ->allowedIncludes(['users', 'branch', 'service', 'latestOccurrence.status'])
            ->allowedSorts([
                'created_at',
                'updated_at',
                AllowedSort::custom('latest_due_date', new LatestOccurrenceDueDateSort()),
                AllowedSort::custom('latest_start_date', new LatestOccurrenceStartDateSort()),
                AllowedSort::custom('latest_end_date', new LatestOccurrenceEndDateSort()),
            ])
            ->with(['branch', 'service', 'latestOccurrence.status'])
            ->orderByDesc($latestOccurrenceUpdatedAt)
            ->orderByDesc('updated_at');
    }

    protected function buildTasksQuery($branchIds)
    {
        $latestOccurrenceUpdatedAt = $this->latestOccurrenceTimestampSubquery();

        return QueryBuilder::for(Task::class)
            ->whereIn('branch_id', $branchIds)
            ->allowedIncludes(["users", "branch", "service", "latestOccurrence.status"])
            ->allowedSorts([
                AllowedSort::custom('latest_due_date', new LatestOccurrenceDueDateSort()),
                AllowedSort::custom('latest_start_date', new LatestOccurrenceStartDateSort()),
                AllowedSort::custom('latest_end_date', new LatestOccurrenceEndDateSort()),
                "created_at",
            ])
            ->allowedFilters($this->taskFiltersConfig())
            ->with(["users", "branch", "service", "latestOccurrence.status"])
            ->orderByDesc($latestOccurrenceUpdatedAt);
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
        ];
    }
    protected function taskSortableMap(): array
    {
        return [
            'განმეორების თარიღი' => 'latest_due_date',
            'დაწყება' => 'latest_start_date',
            'დასრულება' => 'latest_end_date',
        ];
    }

    /**
     * Latest occurrence timestamp subquery for ordering tasks based on their latest occurrence timestamp.
     */
    protected function latestOccurrenceTimestampSubquery(string $column = 'updated_at')
    {
        return TaskOccurrence::select($column)
            ->whereColumn('task_occurrences.task_id', 'tasks.id')
            ->latest()
            ->limit(1);
    }
}
