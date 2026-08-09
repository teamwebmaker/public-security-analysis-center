<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Role;
use App\Models\Service;
use App\Models\Task;
use App\Models\TaskOccurrence;
use App\Models\TaskOccurrenceStatus;
use App\Models\TaskWorkerInvitation;
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

        $this->assertSame($worker->id, $createdTask->created_by_user_id);
        $this->assertDatabaseHas('task_workers', [
            'task_id' => $createdTask->id,
            'user_id' => $worker->id,
        ]);
        $this->assertDatabaseHas('task_occurrence_workers', [
            'task_occurrence_id' => $createdOccurrence->id,
            'worker_id_snapshot' => $worker->id,
        ]);
    }

    public function test_task_creator_can_invite_worker_and_acceptance_adds_worker_to_task(): void
    {
        $context = $this->createTaskContext();
        $creator = $context['worker'];
        $task = $context['task'];
        $occurrence = $context['occurrence'];
        $invitee = $this->createWorker('Invited Worker');

        $this->makeWorkerTaskCreator($task, $occurrence, $creator);

        $this->actingAs($creator)
            ->post(route('management.worker.tasks.invitations.store', $task), [
                'invited_worker_id' => $invitee->id,
            ])
            ->assertRedirect();

        $invitation = TaskWorkerInvitation::query()->firstOrFail();

        $this->assertSame(TaskWorkerInvitation::STATUS_PENDING, $invitation->status);
        $this->assertDatabaseMissing('task_workers', [
            'task_id' => $task->id,
            'user_id' => $invitee->id,
        ]);

        $beforeAcceptance = $this->actingAs($invitee)->get(route('management.dashboard.tasks'));
        $beforeAcceptance->assertViewHas(
            'tasks',
            fn($tasks) => !$tasks->getCollection()->contains('id', $task->id)
        );
        $beforeAcceptance->assertSee('სამუშაოზე მოწვევები');

        $this->actingAs($invitee)
            ->post(route('management.worker.task-invitations.accept', $invitation))
            ->assertRedirect();

        $this->assertDatabaseHas('task_worker_invitations', [
            'id' => $invitation->id,
            'status' => TaskWorkerInvitation::STATUS_ACCEPTED,
        ]);
        $this->assertDatabaseHas('task_workers', [
            'task_id' => $task->id,
            'user_id' => $invitee->id,
        ]);
        $this->assertDatabaseHas('task_occurrence_workers', [
            'task_occurrence_id' => $occurrence->id,
            'worker_id_snapshot' => $invitee->id,
            'worker_name_snapshot' => $invitee->full_name,
        ]);

        $afterAcceptance = $this->actingAs($invitee)->get(route('management.dashboard.tasks'));
        $afterAcceptance->assertViewHas(
            'tasks',
            fn($tasks) => $tasks->getCollection()->contains('id', $task->id)
        );
    }

    public function test_worker_who_did_not_create_task_cannot_invite_others(): void
    {
        $context = $this->createTaskContext();
        $invitee = $this->createWorker('Invitee');

        $this->actingAs($context['worker'])
            ->post(route('management.worker.tasks.invitations.store', $context['task']), [
                'invited_worker_id' => $invitee->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('task_worker_invitations', 0);
    }

    public function test_only_invited_worker_can_accept_invitation(): void
    {
        $context = $this->createTaskContext();
        $creator = $context['worker'];
        $invitee = $this->createWorker('Invited Worker');
        $unrelatedWorker = $this->createWorker('Unrelated Worker');

        $this->makeWorkerTaskCreator($context['task'], $context['occurrence'], $creator);

        $invitation = TaskWorkerInvitation::create([
            'task_id' => $context['task']->id,
            'inviter_id' => $creator->id,
            'invited_worker_id' => $invitee->id,
            'status' => TaskWorkerInvitation::STATUS_PENDING,
        ]);

        $this->actingAs($unrelatedWorker)
            ->post(route('management.worker.task-invitations.accept', $invitation))
            ->assertForbidden();

        $this->assertDatabaseMissing('task_workers', [
            'task_id' => $context['task']->id,
            'user_id' => $unrelatedWorker->id,
        ]);
        $this->assertSame(
            TaskWorkerInvitation::STATUS_PENDING,
            $invitation->fresh()->status
        );
    }

    public function test_declining_invitation_does_not_assign_worker(): void
    {
        $context = $this->createTaskContext();
        $creator = $context['worker'];
        $invitee = $this->createWorker('Invited Worker');

        $this->makeWorkerTaskCreator($context['task'], $context['occurrence'], $creator);

        $invitation = TaskWorkerInvitation::create([
            'task_id' => $context['task']->id,
            'inviter_id' => $creator->id,
            'invited_worker_id' => $invitee->id,
            'status' => TaskWorkerInvitation::STATUS_PENDING,
        ]);

        $this->actingAs($invitee)
            ->post(route('management.worker.task-invitations.decline', $invitation))
            ->assertRedirect();

        $this->assertDatabaseHas('task_worker_invitations', [
            'id' => $invitation->id,
            'status' => TaskWorkerInvitation::STATUS_DECLINED,
        ]);
        $this->assertDatabaseMissing('task_workers', [
            'task_id' => $context['task']->id,
            'user_id' => $invitee->id,
        ]);
        $this->assertDatabaseMissing('task_occurrence_workers', [
            'task_occurrence_id' => $context['occurrence']->id,
            'worker_id_snapshot' => $invitee->id,
        ]);
    }

    /**
     * @return array{worker: User, otherWorker: User, branch: Branch, service: Service, task: Task, occurrence: TaskOccurrence}
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

        $worker->workerCompanies()->attach($company);

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

        return compact('worker', 'otherWorker', 'branch', 'service', 'task', 'occurrence');
    }

    private function createWorker(string $fullName): User
    {
        return User::create([
            'full_name' => $fullName,
            'email' => uniqid('worker-', true) . '@example.test',
            'phone' => '5' . random_int(10000000, 99999999),
            'password' => 'secret',
            'role_id' => Role::query()->where('name', 'worker')->value('id'),
            'is_active' => true,
        ]);
    }

    private function makeWorkerTaskCreator(Task $task, TaskOccurrence $occurrence, User $creator): void
    {
        $task->update(['created_by_user_id' => $creator->id]);
        $task->users()->syncWithoutDetaching([$creator->id]);
        $occurrence->workers()->firstOrCreate(
            ['worker_id_snapshot' => $creator->id],
            ['worker_name_snapshot' => $creator->full_name]
        );
    }
}
