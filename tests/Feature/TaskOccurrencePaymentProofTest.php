<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Message;
use App\Models\Role;
use App\Models\Service;
use App\Models\Task;
use App\Models\TaskOccurrence;
use App\Models\TaskOccurrenceStatus;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TaskOccurrenceStatusSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskOccurrencePaymentProofTest extends TestCase
{
    private User $admin;

    private User $responsiblePerson;

    private User $otherResponsiblePerson;

    private Branch $branch;

    private Service $service;

    private Task $task;

    private TaskOccurrence $occurrence;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh');
        $this->seed([RoleSeeder::class, TaskOccurrenceStatusSeeder::class]);
        Storage::fake('local');
        Bus::fake();
        Carbon::setTestNow(Carbon::parse('2026-09-16 08:30:00', 'UTC'));

        $this->admin = $this->createUser('admin', 'Main Admin', '500000101');
        $this->responsiblePerson = $this->createUser(
            'responsible_person',
            'Responsible Person',
            '500000102'
        );
        $this->otherResponsiblePerson = $this->createUser(
            'responsible_person',
            'Other Responsible Person',
            '500000103'
        );

        $company = Company::query()->create([
            'name' => 'Payment Company',
            'economic_activity_type_id' => null,
            'identification_code' => 'PAYMENT-001',
            'economic_activity_code' => '123456',
            'high_risk_activities' => false,
            'risk_level' => 'low',
            'evacuation_plan' => true,
            'visibility' => '1',
        ]);
        $this->branch = Branch::query()->create([
            'name' => 'Payment Branch',
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

        $this->responsiblePerson->branches()->attach($this->branch);
        $this->responsiblePerson->services()->attach($this->service);

        $this->task = Task::query()->create([
            'branch_id' => $this->branch->id,
            'branch_name_snapshot' => $this->branch->name,
            'service_id' => $this->service->id,
            'service_name_snapshot' => $this->service->title->ka,
            'recurrence_interval' => 30,
            'is_recurring' => true,
            'archived' => '0',
            'visibility' => '1',
        ]);
        $this->occurrence = TaskOccurrence::query()->create([
            'task_id' => $this->task->id,
            'branch_id_snapshot' => $this->branch->id,
            'branch_name_snapshot' => $this->branch->name,
            'service_id_snapshot' => $this->service->id,
            'service_name_snapshot' => $this->service->title->ka,
            'due_date' => '2026-09-20',
            'status_id' => TaskOccurrenceStatus::query()->where('name', 'pending')->value('id'),
            'requires_document' => false,
            'payment_status' => 'unpaid',
            'visibility' => '1',
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_connected_responsible_person_can_upload_payment_proof_and_admin_is_notified(): void
    {
        $response = $this->actingAs($this->responsiblePerson)->post(
            route('management.task-occurrences.payment-proof.store', $this->occurrence),
            ['payment_proof' => UploadedFile::fake()->image('receipt.png')]
        );

        $response->assertRedirect()->assertSessionHas('success');
        $this->occurrence->refresh();

        $this->assertSame('pending', $this->occurrence->payment_status);
        $this->assertSame('receipt.png', $this->occurrence->payment_proof_original_name);
        $this->assertSame($this->responsiblePerson->id, $this->occurrence->payment_proof_uploaded_by_user_id);
        $this->assertNotNull($this->occurrence->payment_proof_uploaded_at);
        Storage::disk('local')->assertExists($this->occurrence->payment_proof_path);

        $message = Message::query()->latest('id')->firstOrFail();
        $this->assertSame('system', $message->source);
        $this->assertSame('payment', $message->type);
        $this->assertSame('გადახდის დამადასტურებელი დოკუმენტი აიტვირთა', $message->subject);
        $this->assertStringStartsWith('💰 გადახდის შეტყობინება:', $message->message);
        $this->assertStringNotContainsString('🤖', $message->message);
        $this->assertStringContainsString('ვინ: Responsible Person', $message->message);
        $this->assertStringContainsString('როდის: 16.09.2026 12:30', $message->message);
        $this->assertStringContainsString("დანიშნულება: საქმე #{$this->occurrence->id}-ის გადახდა", $message->message);
        $this->assertStringContainsString('ფილიალი: Payment Branch', $message->message);
        $this->assertStringContainsString('სერვისი: რისკების შეფასება', $message->message);

        $this->actingAs($this->admin)
            ->get(route('messages.index', ['filter' => ['source' => 'payment']]))
            ->assertOk()
            ->assertSee('💰 გადახდა')
            ->assertSee($message->subject);

        $this->actingAs($this->admin)
            ->get(route('messages.index', ['filter' => ['source' => 'system']]))
            ->assertOk()
            ->assertDontSee($message->subject);

        $this->actingAs($this->responsiblePerson)
            ->get(route('management.dashboard.page'))
            ->assertOk()
            ->assertSee('დასტურის შეცვლა')
            ->assertSee('receipt.png');
    }

    public function test_unconnected_users_and_paid_occurrences_cannot_upload_payment_proof(): void
    {
        $this->actingAs($this->otherResponsiblePerson)
            ->post(
                route('management.task-occurrences.payment-proof.store', $this->occurrence),
                ['payment_proof' => UploadedFile::fake()->image('unauthorized.png')]
            )
            ->assertForbidden();

        $this->occurrence->update(['payment_status' => 'paid']);

        $this->actingAs($this->responsiblePerson)
            ->post(
                route('management.task-occurrences.payment-proof.store', $this->occurrence),
                ['payment_proof' => UploadedFile::fake()->image('paid.png')]
            )
            ->assertForbidden();

        $this->assertNull($this->occurrence->fresh()->payment_proof_path);
        $this->assertDatabaseCount('messages', 0);
    }

    public function test_payment_proof_is_private_and_admin_can_review_then_confirm_payment(): void
    {
        $this->actingAs($this->responsiblePerson)->post(
            route('management.task-occurrences.payment-proof.store', $this->occurrence),
            ['payment_proof' => UploadedFile::fake()->create('transfer.pdf', 50, 'application/pdf')]
        )->assertRedirect();

        $this->occurrence->refresh();

        $this->actingAs($this->responsiblePerson)
            ->get(route('management.task-occurrences.payment-proof.show', $this->occurrence))
            ->assertOk();

        $this->actingAs($this->otherResponsiblePerson)
            ->get(route('management.task-occurrences.payment-proof.show', $this->occurrence))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('task-occurrences.payment-proof.show', $this->occurrence))
            ->assertOk();

        $this->actingAs($this->admin)
            ->get(route('task-occurrences.payment-proof.download', $this->occurrence))
            ->assertDownload('transfer.pdf');

        $this->actingAs($this->admin)
            ->get(route('tasks.occurrences', $this->task))
            ->assertOk()
            ->assertSee('გადახდის დასტური')
            ->assertSee(route('task-occurrences.payment-proof.show', $this->occurrence));

        $this->actingAs($this->admin)
            ->put(route('task-occurrences.mark-paid', $this->occurrence))
            ->assertRedirect();

        $this->assertSame('paid', $this->occurrence->fresh()->payment_status);
        Storage::disk('local')->assertExists($this->occurrence->payment_proof_path);
    }

    public function test_payment_proof_validation_accepts_only_supported_documents(): void
    {
        $this->actingAs($this->responsiblePerson)
            ->post(
                route('management.task-occurrences.payment-proof.store', $this->occurrence),
                ['payment_proof' => UploadedFile::fake()->create('receipt.txt', 10, 'text/plain')]
            )
            ->assertSessionHasErrors('payment_proof');

        $this->assertNull($this->occurrence->fresh()->payment_proof_path);
    }

    private function createUser(string $role, string $name, string $phone): User
    {
        return User::query()->create([
            'full_name' => $name,
            'email' => strtolower(str_replace(' ', '-', $name)).'@example.test',
            'phone' => $phone,
            'password' => 'password',
            'role_id' => Role::query()->where('name', $role)->value('id'),
            'is_active' => true,
        ]);
    }
}
