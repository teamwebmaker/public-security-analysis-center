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

class AdminDashboardUserSummaryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh');
        $this->seed([
            RoleSeeder::class,
            TaskOccurrenceStatusSeeder::class,
        ]);
    }

    public function test_admin_can_view_role_aware_worker_summary(): void
    {
        $admin = $this->createUser('admin', 'Admin User');
        $worker = $this->createUser('worker', '<script>Worker</script>');
        $company = Company::create([
            'name' => 'ACME',
            'identification_code' => 'ID-1000',
            'economic_activity_code' => '123456',
            'high_risk_activities' => false,
            'risk_level' => 'low',
            'evacuation_plan' => true,
            'visibility' => '1',
        ]);
        $worker->workerCompanies()->attach($company);
        $branch = Branch::create([
            'name' => 'Central Branch',
            'address' => 'Main Street 1',
            'company_id' => $company->id,
            'visibility' => '1',
        ]);
        $service = Service::create([
            'title' => ['ka' => 'სერვისი', 'en' => 'Service'],
            'description' => ['ka' => 'აღწერა', 'en' => 'Description'],
            'image' => 'service.png',
            'visibility' => '1',
            'sortable' => 1,
        ]);
        $task = Task::create([
            'branch_id' => $branch->id,
            'branch_name_snapshot' => $branch->name,
            'service_id' => $service->id,
            'service_name_snapshot' => 'სერვისი',
            'is_recurring' => false,
            'archived' => '0',
            'visibility' => '1',
        ]);
        $task->users()->attach($worker);
        TaskOccurrence::create([
            'task_id' => $task->id,
            'branch_id_snapshot' => $branch->id,
            'branch_name_snapshot' => $branch->name,
            'service_id_snapshot' => $service->id,
            'service_name_snapshot' => 'სერვისი',
            'status_id' => $this->statusId('pending'),
            'due_date' => now()->addWeek()->toDateString(),
            'requires_document' => false,
            'payment_status' => 'unpaid',
            'visibility' => '1',
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('users.dashboard-summary', $worker));

        $response
            ->assertOk()
            ->assertJsonPath('user.full_name', '<script>Worker</script>')
            ->assertJsonPath('user.role.name', 'worker')
            ->assertJsonPath('connections.label', 'საქმის შექმნის კომპანიები')
            ->assertJsonPath('connections.total', 1)
            ->assertJsonPath('tasks.0.id', $task->id)
            ->assertJsonPath('tasks.0.occurrences_url', route('tasks.index', [
                'occurrences_task_id' => $task->id,
            ]))
            ->assertJsonFragment([
                'label' => 'აქტიური საქმეები',
                'value' => 1,
            ]);
    }

    public function test_dashboard_user_filter_and_summary_route_are_admin_only(): void
    {
        $admin = $this->createUser('admin', 'Admin User');
        $worker = $this->createUser('worker', 'Worker User');
        $leader = $this->createUser('company_leader', 'Leader User');

        $this->actingAs($admin)
            ->get(route('admin.dashboard.page', ['filter' => ['role_id' => $worker->role_id]]))
            ->assertOk()
            ->assertSee('Worker User')
            ->assertDontSee('Leader User');

        $this->actingAs($worker)
            ->get(route('users.dashboard-summary', $leader))
            ->assertRedirect(route('admin.login.page'));
    }

    private function createUser(string $roleName, string $fullName): User
    {
        return User::create([
            'full_name' => $fullName,
            'email' => strtolower(str_replace(' ', '.', strip_tags($fullName))) . ".{$roleName}@example.test",
            'phone' => '50000000' . Role::query()->where('name', $roleName)->value('id'),
            'password' => 'secret',
            'role_id' => Role::query()->where('name', $roleName)->value('id'),
            'is_active' => true,
        ]);
    }

    private function statusId(string $name): int
    {
        return (int) TaskOccurrenceStatus::query()->where('name', $name)->value('id');
    }
}
