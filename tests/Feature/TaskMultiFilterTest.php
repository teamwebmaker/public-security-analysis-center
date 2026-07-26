<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Role;
use App\Models\Service;
use App\Models\Task;
use App\Models\TaskOccurrence;
use App\Models\TaskOccurrenceStatus;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TaskOccurrenceStatusSeeder;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class TaskMultiFilterTest extends TestCase
{
    private Company $companyA;
    private Company $companyB;
    private Branch $branchA;
    private Branch $branchB;
    private Service $serviceA;
    private Service $serviceB;
    private Service $serviceC;
    private Task $taskA;
    private Task $taskAOtherService;
    private Task $taskB;
    private User $admin;
    private User $companyLeader;
    private User $responsiblePerson;
    private User $workerA;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh');
        $this->seed([
            RoleSeeder::class,
            TaskOccurrenceStatusSeeder::class,
        ]);

        $this->companyA = $this->createCompany('Company A', 'COMP-A');
        $this->companyB = $this->createCompany('Company B', 'COMP-B');
        $this->branchA = $this->createBranch($this->companyA, 'Branch A');
        $this->branchB = $this->createBranch($this->companyB, 'Branch B');
        $this->serviceA = $this->createService('Service A', 1);
        $this->serviceB = $this->createService('Service B', 2);
        $this->serviceC = $this->createService('Service C', 3);

        $this->admin = $this->createUser('admin', 'Admin User');
        $this->companyLeader = $this->createUser('company_leader', 'Company Leader');
        $this->responsiblePerson = $this->createUser('responsible_person', 'Responsible Person');
        $this->workerA = $this->createUser('worker', 'Worker A');
        $workerB = $this->createUser('worker', 'Worker B');

        $this->companyLeader->companies()->attach($this->companyA);
        $this->responsiblePerson->branches()->attach($this->branchA);
        $this->responsiblePerson->services()->attach($this->serviceA);

        $this->taskA = $this->createTask($this->branchA, $this->serviceA, $this->workerA);
        $this->taskAOtherService = $this->createTask($this->branchA, $this->serviceB, $this->workerA);
        $this->taskB = $this->createTask($this->branchB, $this->serviceC, $workerB);
    }

    public function test_admin_can_combine_company_branch_and_service_filters(): void
    {
        $response = $this->actingAs($this->admin)->get(route('tasks.index', [
            'filter' => $this->entityFilterQuery(
                $this->companyA,
                $this->branchA,
                $this->serviceA
            ),
        ]));

        $response->assertOk();
        $this->assertTaskIds($response->viewData('tasks'), [$this->taskA->id]);
    }

    public function test_company_leader_filters_are_scoped_to_connected_companies(): void
    {
        $response = $this->actingAs($this->companyLeader)->get(route('management.dashboard.tasks', [
            'filter' => $this->entityFilterQuery(
                $this->companyA,
                $this->branchA,
                $this->serviceA
            ),
        ]));

        $response->assertOk();
        $this->assertTaskIds($response->viewData('tasks'), [$this->taskA->id]);
        $this->assertScopedFilterOptions($response->viewData('filters'), [
            'company_id' => [$this->companyA->id],
            'branch_id' => [$this->branchA->id],
            'service_id' => [$this->serviceA->id, $this->serviceB->id],
        ]);

        $tampered = $this->actingAs($this->companyLeader)->get(route('management.dashboard.tasks', [
            'filter' => [
                'company_id' => $this->companyB->id,
                'branch_id' => $this->branchB->id,
                'service_id' => $this->serviceC->id,
            ],
        ]));

        $tampered->assertOk();
        $this->assertTaskIds($tampered->viewData('tasks'), []);
    }

    public function test_responsible_person_filters_and_options_remain_authorized(): void
    {
        $response = $this->actingAs($this->responsiblePerson)->get(route('management.dashboard.tasks', [
            'filter' => $this->entityFilterQuery(
                $this->companyA,
                $this->branchA,
                $this->serviceA
            ),
        ]));

        $response->assertOk();
        $this->assertTaskIds($response->viewData('tasks'), [$this->taskA->id]);
        $this->assertScopedFilterOptions($response->viewData('filters'), [
            'company_id' => [$this->companyA->id],
            'branch_id' => [$this->branchA->id],
            'service_id' => [$this->serviceA->id],
        ]);

        $tampered = $this->actingAs($this->responsiblePerson)->get(route('management.dashboard.tasks', [
            'filter' => [
                'company_id' => $this->companyB->id,
                'branch_id' => $this->branchB->id,
                'service_id' => $this->serviceC->id,
            ],
        ]));

        $tampered->assertOk();
        $this->assertTaskIds($tampered->viewData('tasks'), []);
    }

    public function test_worker_filters_only_tasks_assigned_to_them(): void
    {
        $response = $this->actingAs($this->workerA)->get(route('management.dashboard.tasks', [
            'filter' => $this->entityFilterQuery(
                $this->companyA,
                $this->branchA,
                $this->serviceA
            ),
        ]));

        $response->assertOk();
        $this->assertTaskIds($response->viewData('tasks'), [$this->taskA->id]);
        $this->assertScopedFilterOptions($response->viewData('filters'), [
            'company_id' => [$this->companyA->id],
            'branch_id' => [$this->branchA->id],
            'service_id' => [$this->serviceA->id, $this->serviceB->id],
        ]);

        $tampered = $this->actingAs($this->workerA)->get(route('management.dashboard.tasks', [
            'filter' => [
                'company_id' => $this->companyB->id,
                'branch_id' => $this->branchB->id,
                'service_id' => $this->serviceC->id,
            ],
        ]));

        $tampered->assertOk();
        $this->assertTaskIds($tampered->viewData('tasks'), []);
    }

    public function test_filter_interface_supports_multiple_rows_and_preserves_search_state(): void
    {
        $response = $this->actingAs($this->workerA)->get(route('management.dashboard.tasks', [
            'filter' => [
                'company_id' => $this->companyA->id,
                'branch_id' => $this->branchA->id,
                'search' => 'Service',
            ],
            'sort' => 'latest_start_date',
        ]));

        $response->assertOk();
        $response->assertSee('ფილტრის დამატება');
        $response->assertSee('ფილტრების გამოყენება');
        $response->assertSee('ყველა ფილტრის გასუფთავება');
        $response->assertSee('name="filter[company_id]"', false);
        $response->assertSee('name="filter[branch_id]"', false);
        $response->assertSee('name="filter[search]"', false);
        $response->assertSee('name="sort"', false);
    }

    public function test_result_count_component_displays_filtered_total_and_zero(): void
    {
        $withResults = view('components.shared.result-count', ['count' => 24])->render();
        $withoutResults = view('components.shared.result-count', ['count' => 0])->render();

        $this->assertStringContainsString('სულ:', $withResults);
        $this->assertStringContainsString('>24</strong>', $withResults);
        $this->assertStringContainsString('სულ:', $withoutResults);
        $this->assertStringContainsString('>0</strong>', $withoutResults);
    }

    private function entityFilterQuery(
        Company $company,
        Branch $branch,
        Service $service
    ): array {
        return [
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'service_id' => $service->id,
        ];
    }

    private function assertTaskIds($paginator, array $expectedIds): void
    {
        $actualIds = $paginator->getCollection()
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->sort()
            ->values()
            ->all();

        sort($expectedIds);
        $this->assertSame($expectedIds, $actualIds);
        $this->assertSame(count($expectedIds), $paginator->total());
    }

    private function assertScopedFilterOptions(array $filters, array $expected): void
    {
        foreach ($expected as $filterKey => $expectedIds) {
            $actualIds = collect(array_keys($filters[$filterKey]['options']))
                ->map(fn($id) => (int) $id)
                ->sort()
                ->values()
                ->all();

            sort($expectedIds);
            $this->assertSame($expectedIds, $actualIds, "Unexpected {$filterKey} options.");
        }
    }

    private function createCompany(string $name, string $code): Company
    {
        return Company::create([
            'name' => $name,
            'economic_activity_type_id' => null,
            'identification_code' => $code,
            'economic_activity_code' => '123456',
            'high_risk_activities' => false,
            'risk_level' => 'low',
            'evacuation_plan' => true,
            'visibility' => '1',
        ]);
    }

    private function createBranch(Company $company, string $name): Branch
    {
        return Branch::create([
            'name' => $name,
            'address' => "{$name} Address",
            'company_id' => $company->id,
            'visibility' => '1',
        ]);
    }

    private function createService(string $name, int $sortOrder): Service
    {
        return Service::create([
            'title' => ['ka' => $name, 'en' => $name],
            'description' => ['ka' => 'აღწერა', 'en' => 'Description'],
            'image' => 'service.png',
            'document' => null,
            'service_category_id' => null,
            'visibility' => '1',
            'sortable' => $sortOrder,
        ]);
    }

    private function createUser(string $role, string $fullName): User
    {
        return User::create([
            'full_name' => $fullName,
            'email' => uniqid(str_replace(' ', '-', strtolower($fullName)) . '-', true) . '@example.test',
            'phone' => '5' . random_int(10000000, 99999999),
            'password' => 'secret',
            'role_id' => Role::query()->where('name', $role)->value('id'),
            'is_active' => true,
        ]);
    }

    private function createTask(Branch $branch, Service $service, User $worker): Task
    {
        $task = Task::create([
            'branch_id' => $branch->id,
            'branch_name_snapshot' => $branch->name,
            'service_id' => $service->id,
            'service_name_snapshot' => $service->title->ka,
            'recurrence_interval' => null,
            'is_recurring' => false,
            'archived' => '0',
            'visibility' => '1',
        ]);
        $task->users()->attach($worker);

        $occurrence = TaskOccurrence::create([
            'task_id' => $task->id,
            'branch_id_snapshot' => $branch->id,
            'branch_name_snapshot' => $branch->name,
            'service_id_snapshot' => $service->id,
            'service_name_snapshot' => $service->title->ka,
            'status_id' => TaskOccurrenceStatus::query()->where('name', 'pending')->value('id'),
            'requires_document' => false,
            'payment_status' => 'unpaid',
            'visibility' => '1',
        ]);
        $occurrence->workers()->create([
            'worker_id_snapshot' => $worker->id,
            'worker_name_snapshot' => $worker->full_name,
        ]);

        return $task;
    }
}
