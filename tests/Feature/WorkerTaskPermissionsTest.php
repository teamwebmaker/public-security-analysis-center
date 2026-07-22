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
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class WorkerTaskPermissionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh');

        $this->seed([
            RoleSeeder::class,
            TaskOccurrenceStatusSeeder::class,
        ]);

        Bus::fake();
        Http::fake();

        config([
            'services.senderge.base_url' => 'https://sender.ge/api',
            'services.senderge.apikey' => 'test-key',
        ]);
    }

    public function test_worker_self_assignment_routes_are_not_registered(): void
    {
        $this->assertFalse(Route::has('management.worker.tasks.assign-self'));
        $this->assertFalse(Route::has('management.worker.tasks.remove-self'));
    }

    public function test_worker_cannot_join_or_leave_tasks_through_old_endpoints(): void
    {
        ['worker' => $worker, 'task' => $task, 'occurrence' => $occurrence] = $this->createTaskContext();

        $this->actingAs($worker)
            ->post("/management/tasks/{$task->id}/assign-self")
            ->assertNotFound();

        $this->assertDatabaseMissing('task_workers', [
            'task_id' => $task->id,
            'user_id' => $worker->id,
        ]);

        $task->users()->attach($worker);
        $occurrence->workers()->create([
            'worker_id_snapshot' => $worker->id,
            'worker_name_snapshot' => $worker->full_name,
        ]);

        $this->actingAs($worker)
            ->delete("/management/tasks/{$task->id}/assign-self")
            ->assertNotFound();

        $this->assertDatabaseHas('task_workers', [
            'task_id' => $task->id,
            'user_id' => $worker->id,
        ]);
        $this->assertDatabaseHas('task_occurrence_workers', [
            'task_occurrence_id' => $occurrence->id,
            'worker_id_snapshot' => $worker->id,
        ]);
    }

    public function test_worker_cannot_view_tasks_they_are_not_assigned_to(): void
    {
        ['worker' => $worker, 'task' => $task] = $this->createTaskContext();

        $response = $this->actingAs($worker)->get(route('management.dashboard.tasks', [
            'tab' => 'available',
        ]));

        $response->assertOk();
        $response->assertDontSee('სხვა აქტიური სამუშაოები');
        $response->assertViewHas(
            'tasks',
            fn($tasks) => !$tasks->getCollection()->contains('id', $task->id)
        );
        $response->assertDontSee('მიმაგრება');
        $response->assertDontSee('მოშორება');
        $response->assertDontSee('/assign-self', false);
    }

    public function test_worker_is_automatically_assigned_to_a_task_they_create(): void
    {
        ['worker' => $worker, 'branch' => $branch, 'service' => $service] = $this->createTaskContext();

        $response = $this->actingAs($worker)->post(route('management.worker.tasks.store'), [
            'service_mode' => 'existing',
            'service_id' => $service->id,
            'branch_id' => $branch->id,
            'is_recurring' => '0',
            'requires_document' => '0',
        ]);

        $response->assertRedirect(route('management.dashboard.tasks'));

        $createdTask = Task::query()->latest('id')->firstOrFail();
        $createdOccurrence = $createdTask->latestOccurrence()->firstOrFail();

        $this->assertDatabaseHas('task_workers', [
            'task_id' => $createdTask->id,
            'user_id' => $worker->id,
        ]);
        $this->assertDatabaseHas('task_occurrence_workers', [
            'task_occurrence_id' => $createdOccurrence->id,
            'worker_id_snapshot' => $worker->id,
        ]);
    }

    /**
     * @return array{worker: User, branch: Branch, service: Service, task: Task, occurrence: TaskOccurrence}
     */
    private function createTaskContext(): array
    {
        $company = Company::create([
            'name' => 'ACME',
            'economic_activity_type_id' => null,
            'identification_code' => 'ID-' . uniqid(),
            'economic_activity_code' => '123456',
            'high_risk_activities' => false,
            'risk_level' => 'low',
            'evacuation_plan' => true,
            'visibility' => '1',
        ]);

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
            'document' => null,
            'service_category_id' => null,
            'visibility' => '1',
            'sortable' => 1,
        ]);

        $worker = User::create([
            'full_name' => 'Worker User',
            'email' => 'worker-' . uniqid() . '@example.test',
            'phone' => '500000002',
            'password' => 'secret',
            'role_id' => Role::query()->where('name', 'worker')->value('id'),
            'is_active' => true,
        ]);

        $otherWorker = User::create([
            'full_name' => 'Other Worker',
            'email' => 'other-' . uniqid() . '@example.test',
            'phone' => '500000003',
            'password' => 'secret',
            'role_id' => Role::query()->where('name', 'worker')->value('id'),
            'is_active' => true,
        ]);

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
        $task->users()->attach($otherWorker);

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
            'worker_id_snapshot' => $otherWorker->id,
            'worker_name_snapshot' => $otherWorker->full_name,
        ]);

        return compact('worker', 'branch', 'service', 'task', 'occurrence');
    }
}
