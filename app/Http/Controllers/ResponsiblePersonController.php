<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskOccurrence;
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

        $inProgressCount = $this->countInProgressTasks($branchIds, $allowedServiceIds);

        $branchTableHeaders = TableHeaderDataPresenter::responsiblePersonBranchHeaders();
        $branchTableRows = $userBranches->getCollection()
            ->map(fn($branch) => TableRowDataPresenter::branchRow($branch));

        $paymentOccurrences = TaskOccurrence::query()
            ->whereIn('branch_id_snapshot', $branchIds)
            ->whereIn('service_id_snapshot', $allowedServiceIds)
            ->whereIn('payment_status', ['unpaid', 'pending', 'overdue'])
            ->orderBy('due_date')
            ->paginate(8, ['*'], 'payments_page')
            ->appends(request()->query());

        $paymentHeaders = TableHeaderDataPresenter::responsiblePersonPaymentHeaders();
        $paymentRows = $paymentOccurrences->map(fn($occurrence) => TableRowDataPresenter::responsiblePersonPaymentRow($occurrence));
        $sidebarItems = config('sidebar.responsible-person');

        return view("management.{$this->resourceName}.dashboard", [
            'sidebarItems' => $sidebarItems,
            'userBranches' => $userBranches,
            'branchTableRows' => $branchTableRows,
            'branchTableHeaders' => $branchTableHeaders,
            'inProgressCount' => $inProgressCount,
            'paymentOccurrences' => $paymentOccurrences,
            'paymentHeaders' => $paymentHeaders,
            'paymentRows' => $paymentRows,
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

        $userTableRows = $tasks->map(fn($task) => TableRowDataPresenter::responsiblePersonTaskRow($task));
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

    protected function countInProgressTasks($branchIds, array $allowedServiceIds): int
    {
        return Task::query()
            ->whereIn('branch_id', $branchIds)
            ->whereIn('service_id', $allowedServiceIds)
            ->whereHas('latestOccurrence.status', fn($q) => $q->where('name', 'in_progress'))
            ->count();
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
        $paymentStatusOptions = [
            'paid' => 'გადახდილი',
            'unpaid' => 'გადასახდელი',
            'pending' => 'მოლოდინში',
            'overdue' => 'ვადაგადაცილებული',
        ];

        return [
            'status' => [
                'label' => 'სტატუსი',
                'options' => $statusOptions,
            ],
            'payment_status' => [
                'label' => 'გადახდოს სტატისი',
                'options' => $paymentStatusOptions,
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
            AllowedFilter::callback('payment_status', function ($query, $value) {
                $query->whereHas('latestOccurrence', function ($q) use ($value) {
                    $q->where('payment_status', $value);
                });
            }),
            AllowedFilter::exact('is_recurring'),
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
