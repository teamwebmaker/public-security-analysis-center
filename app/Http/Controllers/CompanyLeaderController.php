<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Presenters\TableRowDataPresenter;
use GuzzleHttp\Psr7\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CompanyLeaderController extends Controller
{
    public $resourceName = 'company-leader';

    public function displayDashboard()
    {
        $user = auth()->user(); // Currently authenticated user

        // Companies related to the user, eager loading branches, tasks (with status), and users
        $userCompanies = $user->companies()->with([
            'branches.tasks.status',
            'branches.users',
        ])->get();

        // Update each branch inside companies with task counts
        $userCompanies->each(function ($company) {
            $company->branches->transform(function ($branch) {
                $pending = $branch->tasks->filter(fn($t) => $t->status && $t->status->name === 'pending')->count();
                $active = $branch->tasks->filter(fn($t) => $t->status && $t->status->name === 'in_progress')->count();
                $completed = $branch->tasks->filter(fn($t) => $t->status && $t->status->name === 'completed')->count();

                $branch->pending_tasks_count = $pending;
                $branch->active_tasks_count = $active;
                $branch->completed_tasks_count = $completed;

                return $branch;
            });
        });

        // Collect all branches across companies
        $allBranches = $userCompanies->pluck('branches')->flatten();

        $branchIds = $user->companies->flatMap->branches->pluck('id');

        $tasks = QueryBuilder::for(Task::class)
            ->whereIn('branch_id', $branchIds)
            ->allowedIncludes(['status', 'users', 'branch', 'service'])
            ->allowedSorts(['start_date', 'end_date',])
            ->latest('created_at') // newest first
            ->take(10)             // only 10 results
            ->get();


        // In-progress tasks
        $inProgressTasks = $allBranches
            ->pluck('tasks')
            ->flatten()
            ->filter(fn($task) => $task->status && $task->status->name === 'in_progress');

        $userBranchMap = [];

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

        // Optional: count
        $uniqueUserCount = count($userBranchMap);

        $userTableRows = $tasks->map(fn($task) => TableRowDataPresenter::format($task, 'management'));

        return view("management.{$this->resourceName}.dashboard", [

            'inProgressTasks' => $inProgressTasks,
            'userCompanies' => $userCompanies,
            'userBranchMap' => $userBranchMap,
            'branchUsersCount' => $uniqueUserCount,
            'tasks' => $tasks,
            'userTableRows' => $userTableRows,
        ]);

    }

    public function displayTasks()
    {
        $user = auth()->user();

        $branchIds = $user->companies->flatMap->branches->pluck('id');
        $tasks = QueryBuilder::for(Task::class)
            ->whereIn('branch_id', $branchIds)

            ->allowedIncludes(["status", "users", "branch", "service"])
            // Allowed sorting fields
            ->allowedSorts([
                "branch_name",
                "service_name",
                "start_date",
                "end_date",
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
                            ) // or `title`, based on your schema
                            ->orWhereHas(
                                "status",
                                fn($q) => $q->where(
                                    "display_name",
                                    "LIKE",
                                    "%$value%"
                                )
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
            ])
            ->with(["status", "users", "branch", "service"])
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
