<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Task;
use App\Models\TaskOccurrence;
use App\Models\TaskOccurrenceStatus;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TaskOccurrenceStatusSeeder;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class EmployeeManagementTest extends TestCase
{
    private Company $company;

    private Company $otherCompany;

    private Branch $branch;

    private Branch $secondBranch;

    private Branch $otherBranch;

    private User $admin;

    private User $companyLeader;

    private User $responsiblePerson;

    private User $worker;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh');
        $this->seed([RoleSeeder::class, TaskOccurrenceStatusSeeder::class]);

        $this->company = $this->createCompany('Visible Company', '100000001');
        $this->otherCompany = $this->createCompany('Other Company', '100000002');
        $this->branch = $this->createBranch($this->company, 'Branch Alpha');
        $this->secondBranch = $this->createBranch($this->company, 'Branch Beta');
        $this->otherBranch = $this->createBranch($this->otherCompany, 'Branch Gamma');

        $this->admin = $this->createUser('admin', 'Main Admin');
        $this->companyLeader = $this->createUser('company_leader', 'Company Leader');
        $this->responsiblePerson = $this->createUser('responsible_person', 'Responsible Person');
        $this->worker = $this->createUser('worker', 'Task Worker');

        $this->companyLeader->companies()->attach($this->company);
        $this->responsiblePerson->branches()->attach($this->branch);
    }

    public function test_admin_company_leader_and_responsible_person_can_create_employees(): void
    {
        $this->actingAs($this->admin)
            ->post(route('employees.store'), $this->employeePayload(
                $this->company,
                [$this->branch, $this->secondBranch],
                'Admin Created'
            ))
            ->assertRedirect();

        $this->actingAs($this->companyLeader)
            ->post(route('management.employees.store'), $this->employeePayload(
                $this->company,
                [$this->branch],
                'Leader Created'
            ))
            ->assertRedirect();

        $this->actingAs($this->responsiblePerson)
            ->post(route('management.employees.store'), $this->employeePayload(
                $this->company,
                [$this->branch],
                'Responsible Created'
            ))
            ->assertRedirect();

        $this->assertDatabaseCount('employees', 3);
        $adminEmployee = Employee::query()->where('name', 'Admin Created')->firstOrFail();
        $this->assertCount(2, $adminEmployee->branches);
        $this->assertSame($this->admin->id, $adminEmployee->created_by_user_id);

        $this->actingAs($this->admin)
            ->get(route('employees.index'))
            ->assertOk()
            ->assertSee('Admin Created')
            ->assertSee('Leader Created')
            ->assertSee('Responsible Created');
    }

    public function test_worker_cannot_create_employees(): void
    {
        $this->actingAs($this->worker)
            ->get(route('management.employees.create'))
            ->assertForbidden();

        $this->actingAs($this->worker)
            ->post(route('management.employees.store'), $this->employeePayload(
                $this->company,
                [$this->branch]
            ))
            ->assertForbidden();

        $this->assertDatabaseCount('employees', 0);
    }

    public function test_creators_can_select_only_companies_and_branches_in_their_scope(): void
    {
        $this->actingAs($this->companyLeader)
            ->get(route('management.employees.create'))
            ->assertOk()
            ->assertSee($this->company->name)
            ->assertSee($this->branch->name)
            ->assertSee($this->secondBranch->name)
            ->assertDontSee($this->otherCompany->name)
            ->assertDontSee($this->otherBranch->name);

        $this->actingAs($this->responsiblePerson)
            ->get(route('management.employees.create'))
            ->assertOk()
            ->assertSee($this->company->name)
            ->assertSee($this->branch->name)
            ->assertDontSee($this->secondBranch->name)
            ->assertDontSee($this->otherBranch->name);

        $this->actingAs($this->companyLeader)
            ->post(route('management.employees.store'), $this->employeePayload(
                $this->otherCompany,
                [$this->otherBranch]
            ))
            ->assertSessionHasErrors(['company_id', 'branch_ids.0']);

        $this->assertDatabaseCount('employees', 0);
    }

    public function test_branches_must_belong_to_the_selected_company(): void
    {
        $payload = $this->employeePayload($this->company, [$this->branch, $this->otherBranch]);

        $this->actingAs($this->admin)
            ->post(route('employees.store'), $payload)
            ->assertSessionHasErrors('branch_ids');

        $this->assertDatabaseCount('employees', 0);
    }

    public function test_company_leaders_and_responsible_people_see_only_connected_employees(): void
    {
        $connected = $this->createEmployee($this->admin, $this->company, [$this->branch], 'Connected Employee');
        $other = $this->createEmployee($this->admin, $this->otherCompany, [$this->otherBranch], 'Other Employee');

        $this->actingAs($this->companyLeader)
            ->get(route('management.employees.index'))
            ->assertOk()
            ->assertSee($connected->name)
            ->assertDontSee($other->name);

        $this->actingAs($this->responsiblePerson)
            ->get(route('management.employees.index'))
            ->assertOk()
            ->assertSee($connected->name)
            ->assertDontSee($other->name);

        $this->actingAs($this->responsiblePerson)
            ->get(route('management.employees.show', $other))
            ->assertForbidden();
    }

    public function test_worker_sees_branch_employees_only_while_assigned_task_is_active(): void
    {
        $employee = $this->createEmployee(
            $this->admin,
            $this->company,
            [$this->branch],
            'Active Branch Employee',
            true
        );
        $otherEmployee = $this->createEmployee(
            $this->admin,
            $this->otherCompany,
            [$this->otherBranch],
            'Inactive Branch Employee',
            true
        );
        $occurrence = $this->createAssignedTask($this->worker, $this->branch, 'pending');

        $this->actingAs($this->worker)
            ->get(route('management.employees.index'))
            ->assertOk()
            ->assertSee($employee->name)
            ->assertDontSee($otherEmployee->name);

        $this->actingAs($this->worker)
            ->get(route('management.employees.show', $employee))
            ->assertOk()
            ->assertSee('private.employee@example.test');

        $occurrence->update([
            'status_id' => TaskOccurrenceStatus::query()->where('name', 'completed')->value('id'),
        ]);

        $this->actingAs($this->worker)
            ->get(route('management.employees.index'))
            ->assertOk()
            ->assertDontSee($employee->name);

        $this->actingAs($this->worker)
            ->get(route('management.employees.show', $employee))
            ->assertForbidden();
    }

    public function test_hidden_personal_details_are_visible_only_to_creator_and_admin(): void
    {
        $creator = $this->responsiblePerson;
        $otherResponsible = $this->createUser('responsible_person', 'Other Responsible');
        $otherResponsible->branches()->attach($this->branch);

        $employee = $this->createEmployee(
            $creator,
            $this->company,
            [$this->branch],
            'Private Employee',
            false
        );
        $creator->branches()->detach($this->branch);

        $this->actingAs($creator)
            ->get(route('management.employees.show', $employee))
            ->assertOk()
            ->assertSee('private.employee@example.test')
            ->assertSee('01010101010');

        $this->actingAs($this->admin)
            ->get(route('employees.show', $employee))
            ->assertOk()
            ->assertSee('private.employee@example.test')
            ->assertSee('01010101010');

        $this->actingAs($otherResponsible)
            ->get(route('management.employees.index'))
            ->assertOk()
            ->assertSee($employee->name)
            ->assertDontSee('private.employee@example.test')
            ->assertDontSee('01010101010');

        $this->actingAs($otherResponsible)
            ->get(route('management.employees.show', $employee))
            ->assertOk()
            ->assertSee('პირადი მონაცემები დამალულია')
            ->assertDontSee('private.employee@example.test')
            ->assertDontSee('01010101010');
    }

    public function test_only_creator_can_change_personal_details_visibility(): void
    {
        $employee = $this->createEmployee(
            $this->responsiblePerson,
            $this->company,
            [$this->branch],
            'Visibility Employee',
            false
        );

        $this->actingAs($this->admin)
            ->get(route('employees.edit', $employee))
            ->assertOk()
            ->assertSee('ხილვადობის შეცვლა მხოლოდ ჩანაწერის შემქმნელს შეუძლია')
            ->assertDontSee('name="personal_details_visible"', false);

        $adminEditPayload = $this->employeePayload($this->company, [$this->branch], 'Admin Edited Name');
        unset($adminEditPayload['personal_details_visible']);

        $this->actingAs($this->admin)
            ->put(route('employees.update', $employee), $adminEditPayload)
            ->assertRedirect(route('employees.show', $employee));

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'name' => 'Admin Edited Name',
            'personal_details_visible' => false,
        ]);

        $adminPayload = $this->employeePayload($this->company, [$this->branch], $employee->name);
        $adminPayload['personal_details_visible'] = '1';

        $this->actingAs($this->admin)
            ->put(route('employees.update', $employee), $adminPayload)
            ->assertSessionHasErrors('personal_details_visible');

        $this->assertFalse($employee->fresh()->personal_details_visible);
        $this->responsiblePerson->branches()->detach($this->branch);

        $creatorPayload = $this->employeePayload($this->company, [$this->branch], $employee->name);
        $creatorPayload['personal_details_visible'] = '1';

        $this->actingAs($this->responsiblePerson)
            ->put(route('management.employees.update', $employee), $creatorPayload)
            ->assertRedirect(route('management.employees.show', $employee));

        $this->assertTrue($employee->fresh()->personal_details_visible);
    }

    private function employeePayload(
        Company $company,
        array $branches,
        string $name = 'Test Employee'
    ): array {
        return [
            'company_id' => $company->id,
            'branch_ids' => collect($branches)->pluck('id')->all(),
            'name' => $name,
            'surname' => 'Surname',
            'position' => 'Safety Specialist',
            'phone' => '555123456',
            'email' => 'employee-'.md5($name).'@example.test',
            'id_number' => '01010101010',
            'personal_details_visible' => '0',
        ];
    }

    private function createEmployee(
        User $creator,
        Company $company,
        array $branches,
        string $name,
        bool $personalDetailsVisible = false
    ): Employee {
        $employee = Employee::create([
            'company_id' => $company->id,
            'created_by_user_id' => $creator->id,
            'name' => $name,
            'surname' => 'Surname',
            'position' => 'Safety Specialist',
            'phone' => '555123456',
            'email' => 'private.employee@example.test',
            'id_number' => '01010101010',
            'personal_details_visible' => $personalDetailsVisible,
        ]);
        $employee->branches()->attach(collect($branches)->pluck('id'));

        return $employee;
    }

    private function createAssignedTask(User $worker, Branch $branch, string $status): TaskOccurrence
    {
        $task = Task::create([
            'created_by_user_id' => $this->admin->id,
            'branch_id' => $branch->id,
            'branch_name_snapshot' => $branch->name,
            'service_id' => null,
            'service_name_snapshot' => 'Temporary Service',
            'recurrence_interval' => null,
            'is_recurring' => false,
            'archived' => false,
            'visibility' => '1',
        ]);
        $task->users()->attach($worker);

        return TaskOccurrence::create([
            'task_id' => $task->id,
            'branch_id_snapshot' => $branch->id,
            'branch_name_snapshot' => $branch->name,
            'service_id_snapshot' => null,
            'service_name_snapshot' => 'Temporary Service',
            'status_id' => TaskOccurrenceStatus::query()->where('name', $status)->value('id'),
            'requires_document' => false,
            'payment_status' => 'unpaid',
            'visibility' => '1',
        ]);
    }

    private function createCompany(string $name, string $code): Company
    {
        return Company::create([
            'name' => $name,
            'economic_activity_type_id' => null,
            'identification_code' => $code,
            'economic_activity_code' => '123',
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
            'address' => 'Tbilisi',
            'company_id' => $company->id,
            'visibility' => '1',
        ]);
    }

    private function createUser(string $role, string $name): User
    {
        return User::create([
            'full_name' => $name,
            'email' => strtolower(str_replace(' ', '-', $name)).'-'.uniqid().'@example.test',
            'phone' => '5'.random_int(10000000, 99999999),
            'password' => 'secret',
            'role_id' => Role::query()->where('name', $role)->value('id'),
            'is_active' => true,
        ]);
    }
}
