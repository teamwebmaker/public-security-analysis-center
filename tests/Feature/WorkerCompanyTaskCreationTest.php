<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Role;
use App\Models\Service;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TaskOccurrenceStatusSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WorkerCompanyTaskCreationTest extends TestCase
{
    private Company $allowedCompany;
    private Company $otherCompany;
    private Branch $allowedBranch;
    private Branch $otherBranch;
    private Service $service;
    private User $worker;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh');
        $this->seed([RoleSeeder::class, TaskOccurrenceStatusSeeder::class]);
        Bus::fake();
        Http::fake();

        $this->allowedCompany = $this->createCompany('Allowed Company', '111111111');
        $this->otherCompany = $this->createCompany('Other Company', '222222222');
        $this->allowedBranch = $this->createBranch($this->allowedCompany, 'Allowed Branch');
        $this->otherBranch = $this->createBranch($this->otherCompany, 'Other Branch');
        $this->service = Service::create([
            'title' => ['ka' => 'სერვისი', 'en' => 'Service'],
            'description' => ['ka' => 'აღწერა', 'en' => 'Description'],
            'image' => 'service.png',
            'document' => null,
            'service_category_id' => null,
            'visibility' => '1',
            'sortable' => 1,
        ]);
        $this->worker = $this->createUser('worker', 'Restricted Worker', '500000021');
        $this->admin = $this->createUser('admin', 'Main Admin', '500000022');
    }

    public function test_admin_can_manage_worker_company_connections(): void
    {
        $response = $this->actingAs($this->admin)->put(route('users.update', $this->worker), [
            'full_name' => $this->worker->full_name,
            'email' => $this->worker->email,
            'phone' => $this->worker->phone,
            'role_id' => $this->worker->role_id,
            'worker_company_ids' => [$this->allowedCompany->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('worker_companies', [
            'user_id' => $this->worker->id,
            'company_id' => $this->allowedCompany->id,
        ]);

        $this->actingAs($this->admin)
            ->get(route('users.edit', $this->worker))
            ->assertOk()
            ->assertSee('სამუშაოების შექმნის კომპანიები')
            ->assertSee($this->allowedCompany->name);
    }

    public function test_admin_users_page_displays_worker_company_connections(): void
    {
        $this->worker->workerCompanies()->attach($this->allowedCompany);

        $this->actingAs($this->admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee('სამუშაოების შექმნის კომპანიები')
            ->assertSee($this->allowedCompany->name);
    }

    public function test_admin_can_create_worker_with_company_connections(): void
    {
        $response = $this->actingAs($this->admin)->post(route('users.store'), [
            'full_name' => 'Connected Worker',
            'email' => 'connected-worker@example.test',
            'phone' => '500000023',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role_id' => $this->worker->role_id,
            'worker_company_ids' => [$this->allowedCompany->id],
        ]);

        $response->assertRedirect(route('users.index'));

        $createdWorker = User::query()
            ->where('email', 'connected-worker@example.test')
            ->firstOrFail();

        $this->assertDatabaseHas('worker_companies', [
            'user_id' => $createdWorker->id,
            'company_id' => $this->allowedCompany->id,
        ]);
    }

    public function test_worker_creation_form_lists_only_connected_company_branches(): void
    {
        $this->worker->workerCompanies()->attach($this->allowedCompany);

        $this->actingAs($this->worker)
            ->get(route('management.worker.tasks.create'))
            ->assertOk()
            ->assertSee($this->allowedBranch->name)
            ->assertDontSee($this->otherBranch->name);
    }

    public function test_worker_can_create_task_for_connected_company(): void
    {
        $this->worker->workerCompanies()->attach($this->allowedCompany);

        $this->actingAs($this->worker)
            ->post(route('management.worker.tasks.store'), $this->taskPayload($this->allowedBranch))
            ->assertRedirect(route('management.dashboard.tasks'));

        $task = Task::query()->firstOrFail();
        $this->assertSame($this->allowedBranch->id, $task->branch_id);
        $this->assertSame($this->worker->id, $task->created_by_user_id);
    }

    public function test_worker_cannot_submit_branch_from_unconnected_company(): void
    {
        $this->worker->workerCompanies()->attach($this->allowedCompany);

        $this->actingAs($this->worker)
            ->post(route('management.worker.tasks.store'), $this->taskPayload($this->otherBranch))
            ->assertSessionHasErrors('branch_id');

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_worker_without_company_connection_cannot_create_task(): void
    {
        $this->actingAs($this->worker)
            ->get(route('management.worker.tasks.create'))
            ->assertOk()
            ->assertSee('ადმინისტრატორმა ჯერ უნდა დაგაკავშიროთ შესაბამის კომპანიასთან')
            ->assertDontSee($this->allowedBranch->name);

        $this->actingAs($this->worker)
            ->post(route('management.worker.tasks.store'), $this->taskPayload($this->allowedBranch))
            ->assertSessionHasErrors('branch_id');

        $this->assertDatabaseCount('tasks', 0);
    }

    private function taskPayload(Branch $branch): array
    {
        return [
            'service_mode' => 'existing',
            'service_id' => $this->service->id,
            'branch_id' => $branch->id,
            'is_recurring' => '0',
            'requires_document' => '0',
        ];
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

    private function createUser(string $role, string $name, string $phone): User
    {
        return User::create([
            'full_name' => $name,
            'email' => strtolower(str_replace(' ', '-', $name)) . '@example.test',
            'phone' => $phone,
            'password' => 'secret',
            'role_id' => Role::query()->where('name', $role)->value('id'),
            'is_active' => true,
        ]);
    }
}
