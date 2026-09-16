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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class TaskPaymentDueDateTest extends TestCase
{
    private User $admin;

    private Branch $branch;

    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh');
        $this->seed([RoleSeeder::class, TaskOccurrenceStatusSeeder::class]);
        Bus::fake();
        Carbon::setTestNow(Carbon::parse('2026-09-16 10:00:00', 'Asia/Tbilisi'));

        $this->admin = User::query()->create([
            'full_name' => 'Main Admin',
            'email' => 'admin@example.test',
            'phone' => '500000001',
            'password' => 'password',
            'role_id' => Role::query()->where('name', 'admin')->value('id'),
            'is_active' => true,
        ]);

        $company = Company::query()->create([
            'name' => 'Due Date Company',
            'economic_activity_type_id' => null,
            'identification_code' => 'DUE-DATE-001',
            'economic_activity_code' => '123456',
            'high_risk_activities' => false,
            'risk_level' => 'low',
            'evacuation_plan' => true,
            'visibility' => '1',
        ]);
        $this->branch = Branch::query()->create([
            'name' => 'Due Date Branch',
            'address' => 'Tbilisi',
            'company_id' => $company->id,
            'visibility' => '1',
        ]);
        $this->service = Service::query()->create([
            'title' => ['ka' => 'რისკების შეფასება', 'en' => 'Risk Assessment'],
            'description' => ['ka' => 'აღწერა', 'en' => 'Description'],
            'image' => 'service.png',
            'document' => null,
            'service_category_id' => null,
            'visibility' => '1',
            'sortable' => 1,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_admin_sets_and_updates_one_time_payment_due_date(): void
    {
        $this->actingAs($this->admin)
            ->post(route('tasks.store'), $this->payload([
                'is_recurring' => '0',
                'due_date' => '2026-10-10',
            ]))
            ->assertRedirect(route('tasks.index'));

        $task = Task::query()->firstOrFail();
        $occurrence = $task->latestOccurrenceWithoutVisibility()->firstOrFail();
        $this->assertSame('2026-10-10', $occurrence->due_date->toDateString());
        $this->assertSame('unpaid', $occurrence->payment_status);

        $this->actingAs($this->admin)
            ->put(route('tasks.update', $task), $this->payload([
                'is_recurring' => '0',
                'due_date' => '2026-10-20',
            ]))
            ->assertRedirect();

        $this->assertSame(
            '2026-10-20',
            $occurrence->fresh()->due_date->toDateString()
        );
    }

    public function test_one_time_task_requires_manual_payment_due_date(): void
    {
        $this->actingAs($this->admin)
            ->post(route('tasks.store'), $this->payload(['is_recurring' => '0']))
            ->assertSessionHasErrors('due_date');

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_recurring_task_calculates_due_date_and_rejects_manual_override(): void
    {
        $this->actingAs($this->admin)
            ->post(route('tasks.store'), $this->payload([
                'is_recurring' => '1',
                'recurrence_interval' => 5,
                'due_date' => '2026-12-31',
            ]))
            ->assertSessionHasErrors('due_date');

        $this->actingAs($this->admin)
            ->post(route('tasks.store'), $this->payload([
                'is_recurring' => '1',
                'recurrence_interval' => 5,
            ]))
            ->assertRedirect(route('tasks.index'));

        $occurrence = Task::query()->firstOrFail()->latestOccurrenceWithoutVisibility()->firstOrFail();
        $this->assertSame('2026-09-21', $occurrence->due_date->toDateString());
    }

    private function payload(array $overrides = []): array
    {
        return [
            'service_mode' => 'existing',
            'service_id' => $this->service->id,
            'branch_id' => $this->branch->id,
            'is_recurring' => '0',
            'requires_document' => '0',
            'visibility' => '1',
            ...$overrides,
        ];
    }
}
