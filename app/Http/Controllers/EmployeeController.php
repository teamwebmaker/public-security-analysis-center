<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Services\Employees\EmployeeBranchAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(private EmployeeBranchAccess $branchAccess) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Employee::class);
        $user = $request->user();
        $search = trim((string) $request->query('search'));

        $employees = Employee::query()
            ->visibleTo($user)
            ->with(['company:id,name', 'branches:id,name', 'creator:id,full_name'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $like = "%{$search}%";
                    $query->where('name', 'LIKE', $like)
                        ->orWhere('surname', 'LIKE', $like)
                        ->orWhere('position', 'LIKE', $like)
                        ->orWhereHas('company', fn ($company) => $company->where('name', 'LIKE', $like))
                        ->orWhereHas('branches', fn ($branch) => $branch->where('name', 'LIKE', $like));
                });
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view($user->isAdmin() ? 'admin.employees.index' : 'management.employees.index', [
            'employees' => $employees,
            'sidebarItems' => $user->isAdmin() ? null : $this->managementSidebar($user),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Employee::class);
        $branches = $this->branchAccess->forUser($request->user());

        return view($request->user()->isAdmin() ? 'admin.employees.create' : 'management.employees.create', [
            'branches' => $branches,
            'companies' => $branches->pluck('company')->unique('id')->sortBy('name')->values(),
            'sidebarItems' => $request->user()->isAdmin() ? null : $this->managementSidebar($request->user()),
        ]);
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $employee = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $branchIds = Arr::pull($data, 'branch_ids');
            $data['created_by_user_id'] = $request->user()->id;

            $employee = Employee::create($data);
            $employee->branches()->sync($branchIds);

            return $employee;
        });

        return redirect()
            ->route($this->routeName($request->user(), 'show'), $employee)
            ->with('success', 'თანამშრომელი წარმატებით შეიქმნა.');
    }

    public function show(Request $request, Employee $employee): View
    {
        $this->authorize('view', $employee);
        $employee->load(['company:id,name', 'branches:id,name', 'creator:id,full_name']);

        return view($request->user()->isAdmin() ? 'admin.employees.show' : 'management.employees.show', [
            'employee' => $employee,
            'sidebarItems' => $request->user()->isAdmin() ? null : $this->managementSidebar($request->user()),
        ]);
    }

    public function edit(Request $request, Employee $employee): View
    {
        $this->authorize('update', $employee);
        $employee->load(['company:id,name', 'branches:id,name']);
        $branches = $this->branchAccess->forUser($request->user(), $employee);

        return view($request->user()->isAdmin() ? 'admin.employees.edit' : 'management.employees.edit', [
            'employee' => $employee,
            'branches' => $branches,
            'companies' => $branches->pluck('company')->unique('id')->sortBy('name')->values(),
            'canChangePersonalDetailsVisibility' => $request->user()->can(
                'changePersonalDetailsVisibility',
                $employee
            ),
            'sidebarItems' => $request->user()->isAdmin() ? null : $this->managementSidebar($request->user()),
        ]);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        DB::transaction(function () use ($request, $employee) {
            $data = $request->validated();
            $branchIds = Arr::pull($data, 'branch_ids');

            if (! $request->user()->can('changePersonalDetailsVisibility', $employee)) {
                unset($data['personal_details_visible']);
            }

            $employee->update($data);
            $employee->branches()->sync($branchIds);
        });

        return redirect()
            ->route($this->routeName($request->user(), 'show'), $employee)
            ->with('success', 'თანამშრომელი წარმატებით განახლდა.');
    }

    public function destroy(Request $request, Employee $employee): RedirectResponse
    {
        $this->authorize('delete', $employee);
        $employee->delete();

        return redirect()
            ->route($this->routeName($request->user(), 'index'))
            ->with('success', 'თანამშრომელი წარმატებით წაიშალა.');
    }

    private function routeName($user, string $action): string
    {
        return $user->isAdmin() ? "employees.{$action}" : "management.employees.{$action}";
    }

    private function managementSidebar($user): array
    {
        return config('sidebar.'.str_replace('_', '-', $user->getRoleName()), []);
    }
}
