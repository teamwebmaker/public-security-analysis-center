<?php

namespace Tests\Feature;

use App\Jobs\MarkOverdueOccurrencePayments;
use App\Jobs\SendUpcomingPaymentReminders;
use App\Models\AdminNumber;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Role;
use App\Models\Service;
use App\Models\SmsLog;
use App\Models\Task;
use App\Models\TaskOccurrence;
use App\Models\TaskOccurrenceStatus;
use App\Models\User;
use App\Services\Tasks\TaskCreator;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TaskOccurrenceStatusSeeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SmsNotificationsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh');

        $this->seed([
            RoleSeeder::class,
            TaskOccurrenceStatusSeeder::class,
        ]);

        Carbon::setTestNow(Carbon::parse('2026-04-17 10:00:00', 'Asia/Tbilisi'));

        config([
            'services.senderge.base_url' => 'https://sender.ge/api',
            'services.senderge.apikey' => 'test-key',
        ]);

        Bus::fake();
        Http::fake([
            'https://sender.ge/*' => Http::response([
                'data' => [
                    [
                        'statusId' => 0,
                        'messageId' => 'provider-message-id',
                    ],
                ],
            ], 200),
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_worker_and_responsible_person_receive_sms_when_task_is_assigned(): void
    {
        ['branch' => $branch, 'service' => $service, 'worker' => $worker, 'responsiblePerson' => $responsiblePerson] = $this->createTaskContext();

        $task = app(TaskCreator::class)->createWithInitialOccurrence([
            'branch_id' => $branch->id,
            'branch_name_snapshot' => $branch->name,
            'service_id' => $service->id,
            'service_name_snapshot' => $service->title->ka,
            'user_ids' => [$worker->id],
            'is_recurring' => true,
            'recurrence_interval' => 5,
            'requires_document' => false,
            'visibility' => '1',
        ]);

        $occurrence = $task->fresh('latestOccurrence')->latestOccurrence;

        $this->assertNotNull($occurrence);
        $this->assertDatabaseHas('sms_logs', [
            'destination' => $worker->phone,
            'event_type' => 'task_assigned',
            'entity_id' => $occurrence->id,
            'recipient_type' => 'worker',
        ]);
        $this->assertDatabaseHas('sms_logs', [
            'destination' => $responsiblePerson->phone,
            'event_type' => 'task_assigned',
            'entity_id' => $occurrence->id,
            'recipient_type' => 'responsible_person',
        ]);
        $this->assertSame(2, SmsLog::query()->where('event_type', 'task_assigned')->count());
        Http::assertSentCount(2);
    }

    public function test_admin_numbers_receive_sms_when_worker_starts_task(): void
    {
        ['task' => $task, 'worker' => $worker, 'occurrence' => $occurrence] = $this->createTaskWithOccurrence([
            'status' => 'pending',
        ]);
        $adminPhones = $this->createAdminNumbers();

        $response = $this->actingAs($worker)->put(route('management.tasks.edit', $task));

        $response->assertRedirect();
        $occurrence->refresh();

        $this->assertSame($this->statusId('in_progress'), $occurrence->status_id);
        $this->assertNotNull($occurrence->start_date);
        $this->assertSame(
            $adminPhones,
            SmsLog::query()
                ->where('event_type', 'task_started')
                ->where('recipient_type', 'admin')
                ->orderBy('destination')
                ->pluck('destination')
                ->all()
        );
        Http::assertSentCount(2);
    }

    public function test_responsible_person_and_admin_numbers_receive_sms_when_task_is_completed(): void
    {
        [
            'task' => $task,
            'worker' => $worker,
            'responsiblePerson' => $responsiblePerson,
            'occurrence' => $occurrence,
        ] = $this->createTaskWithOccurrence([
            'status' => 'in_progress',
            'requires_document' => false,
        ]);
        $adminPhones = $this->createAdminNumbers();

        $response = $this->actingAs($worker)->put(route('management.tasks.upload-document', $task));

        $response->assertRedirect();
        $occurrence->refresh();

        $this->assertSame($this->statusId('completed'), $occurrence->status_id);
        $this->assertNotNull($occurrence->end_date);
        $this->assertDatabaseHas('sms_logs', [
            'destination' => $responsiblePerson->phone,
            'event_type' => 'task_finished',
            'entity_id' => $occurrence->id,
            'recipient_type' => 'responsible_person',
        ]);
        $this->assertSame(
            $adminPhones,
            SmsLog::query()
                ->where('event_type', 'task_finished')
                ->where('recipient_type', 'admin')
                ->orderBy('destination')
                ->pluck('destination')
                ->all()
        );
        Http::assertSentCount(3);
    }

    public function test_responsible_person_receives_payment_reminder_two_days_before_due_date(): void
    {
        ['responsiblePerson' => $responsiblePerson, 'occurrence' => $occurrence] = $this->createTaskWithOccurrence([
            'due_date' => Carbon::now('Asia/Tbilisi')->addDays(2)->toDateString(),
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        app()->call([new SendUpcomingPaymentReminders(), 'handle']);

        $this->assertDatabaseHas('sms_logs', [
            'destination' => $responsiblePerson->phone,
            'event_type' => 'debt_due_2_days',
            'entity_id' => $occurrence->id,
            'recipient_type' => 'responsible_person',
        ]);
        Http::assertSentCount(1);
    }

    public function test_responsible_person_and_admin_numbers_receive_overdue_sms_and_occurrence_is_marked_overdue(): void
    {
        ['responsiblePerson' => $responsiblePerson, 'occurrence' => $occurrence] = $this->createTaskWithOccurrence([
            'due_date' => Carbon::now('Asia/Tbilisi')->subDay()->toDateString(),
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);
        $adminPhones = $this->createAdminNumbers();

        app()->call([new MarkOverdueOccurrencePayments(), 'handle']);

        $occurrence->refresh();

        $this->assertSame('overdue', $occurrence->payment_status);
        $this->assertSame($this->statusId('on_hold'), $occurrence->status_id);
        $this->assertDatabaseHas('sms_logs', [
            'destination' => $responsiblePerson->phone,
            'event_type' => 'debt_overdue_service_suspended',
            'entity_id' => $occurrence->id,
            'recipient_type' => 'responsible_person',
        ]);
        $this->assertSame(
            $adminPhones,
            SmsLog::query()
                ->where('event_type', 'debt_overdue_service_suspended')
                ->where('recipient_type', 'admin')
                ->orderBy('destination')
                ->pluck('destination')
                ->all()
        );
        Http::assertSentCount(3);
    }

    /**
     * @param array{
     *   due_date?: string|null,
     *   payment_status?: string,
     *   requires_document?: bool,
     *   status?: string
     * } $overrides
     * @return array{
     *   task: Task,
     *   worker: User,
     *   responsiblePerson: User,
     *   occurrence: TaskOccurrence
     * }
     */
    private function createTaskWithOccurrence(array $overrides = []): array
    {
        ['task' => $task, 'worker' => $worker, 'responsiblePerson' => $responsiblePerson] = $this->createTaskContext();

        $occurrence = TaskOccurrence::create([
            'task_id' => $task->id,
            'branch_id_snapshot' => $task->branch_id,
            'branch_name_snapshot' => $task->branch_name_snapshot,
            'service_id_snapshot' => $task->service_id,
            'service_name_snapshot' => $task->service_name_snapshot,
            'due_date' => $overrides['due_date'] ?? Carbon::now('Asia/Tbilisi')->addDays(5)->toDateString(),
            'status_id' => $this->statusId($overrides['status'] ?? 'pending'),
            'requires_document' => $overrides['requires_document'] ?? false,
            'payment_status' => $overrides['payment_status'] ?? 'unpaid',
            'visibility' => '1',
        ]);

        $occurrence->workers()->create([
            'worker_id_snapshot' => $worker->id,
            'worker_name_snapshot' => $worker->full_name,
        ]);

        return [
            'task' => $task->fresh('latestOccurrence'),
            'worker' => $worker,
            'responsiblePerson' => $responsiblePerson,
            'occurrence' => $occurrence,
        ];
    }

    /**
     * @return array{
     *   task: Task,
     *   branch: Branch,
     *   service: Service,
     *   worker: User,
     *   responsiblePerson: User
     * }
     */
    private function createTaskContext(): array
    {
        $company = Company::create([
            'name' => 'ACME',
            'economic_activity_type_id' => null,
            'identification_code' => 'ID-1000',
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

        $responsiblePerson = $this->createUser(
            roleName: 'responsible_person',
            fullName: 'Responsible Person',
            email: 'responsible@example.test',
            phone: '500000001'
        );
        $worker = $this->createUser(
            roleName: 'worker',
            fullName: 'Worker User',
            email: 'worker@example.test',
            phone: '500000002'
        );

        $branch->users()->attach($responsiblePerson);
        $responsiblePerson->services()->attach($service);

        $task = Task::create([
            'branch_id' => $branch->id,
            'branch_name_snapshot' => $branch->name,
            'service_id' => $service->id,
            'service_name_snapshot' => $service->title->ka,
            'recurrence_interval' => 5,
            'is_recurring' => true,
            'archived' => '0',
            'visibility' => '1',
        ]);

        $task->users()->attach($worker);

        return [
            'task' => $task,
            'branch' => $branch,
            'service' => $service,
            'worker' => $worker,
            'responsiblePerson' => $responsiblePerson,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function createAdminNumbers(): array
    {
        AdminNumber::create([
            'name' => 'Admin One',
            'phone' => '500000101',
        ]);
        AdminNumber::create([
            'name' => 'Admin Two',
            'phone' => '500000102',
        ]);

        return ['500000101', '500000102'];
    }

    private function createUser(string $roleName, string $fullName, string $email, string $phone): User
    {
        return User::create([
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
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
