<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Role;
use App\Models\User;
use App\Services\Messages\MessageStoreService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class MessageInboxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_filter_open_and_automatically_mark_a_message_as_read(): void
    {
        $admin = $this->createUser('admin');
        $paymentMessage = $this->createMessage([
            'source' => 'system',
            'type' => 'payment',
            'subject' => 'ქვითარი აიტვირთა',
            'message' => 'გადახდის ქვითარი მზად არის შესამოწმებლად.',
            'action_url' => '/admin/tasks?occurrences_task_id=1',
            'action_label' => 'საქმის გახსნა',
        ]);
        $this->createMessage([
            'source' => 'user',
            'type' => 'general',
            'subject' => 'მომხმარებლის წერილი',
            'message' => 'სხვა ტექსტი',
        ]);

        $response = $this->actingAs($admin)->get(route('messages.index', [
            'filter' => [
                'source' => 'payment',
                'read_status' => 'unread',
                'search' => 'ქვითარი',
            ],
            'message' => $paymentMessage->id,
        ]));

        $response
            ->assertOk()
            ->assertSee('ქვითარი აიტვირთა')
            ->assertSee('საქმის გახსნა')
            ->assertDontSee('მომხმარებლის წერილი');
        $this->assertNotNull($paymentMessage->fresh()->read_at);
    }

    public function test_mark_all_read_only_marks_messages_in_the_current_filter(): void
    {
        $admin = $this->createUser('admin');
        $firstPayment = $this->createMessage(['source' => 'system', 'type' => 'payment']);
        $secondPayment = $this->createMessage(['source' => 'system', 'type' => 'payment']);
        $userMessage = $this->createMessage(['source' => 'user', 'type' => 'general']);

        $this->actingAs($admin)
            ->post(route('messages.mark-all-read', ['filter' => ['source' => 'payment']]))
            ->assertRedirect();

        $this->assertNotNull($firstPayment->fresh()->read_at);
        $this->assertNotNull($secondPayment->fresh()->read_at);
        $this->assertNull($userMessage->fresh()->read_at);
    }

    public function test_action_metadata_only_accepts_internal_paths(): void
    {
        Bus::fake();

        $message = app(MessageStoreService::class)->createAndDispatch([
            'source' => 'system',
            'subject' => 'სისტემური შეტყობინება',
            'message' => 'ტესტი',
            'action_url' => '/admin/tasks?occurrences_task_id=10',
            'action_label' => 'საქმის გახსნა',
            'context' => ['task_id' => 10],
        ]);
        $invalidActionMessage = app(MessageStoreService::class)->createAndDispatch([
            'source' => 'system',
            'subject' => 'სხვა შეტყობინება',
            'message' => 'ტესტი',
            'action_url' => 'https://example.test',
        ]);

        $this->assertSame('/admin/tasks?occurrences_task_id=10', $message->action_url);
        $this->assertSame('საქმის გახსნა', $message->action_label);
        $this->assertSame(['task_id' => 10], $message->context);
        $this->assertNull($invalidActionMessage->action_url);
    }

    public function test_non_admin_cannot_open_the_message_inbox(): void
    {
        $worker = $this->createUser('worker');

        $this->actingAs($worker)
            ->get(route('messages.index'))
            ->assertRedirect(route('admin.login.page'));
    }

    private function createMessage(array $overrides = []): Message
    {
        return Message::create(array_merge([
            'source' => 'system',
            'type' => 'general',
            'full_name' => 'system',
            'email' => 'undefined',
            'phone' => 'undefined',
            'subject' => 'შეტყობინება',
            'message' => 'ტესტური შინაარსი',
        ], $overrides));
    }

    private function createUser(string $roleName): User
    {
        return User::create([
            'full_name' => ucfirst($roleName) . ' User',
            'email' => "{$roleName}@example.test",
            'phone' => $roleName === 'admin' ? '500000001' : '500000002',
            'password' => 'secret',
            'role_id' => Role::query()->where('name', $roleName)->value('id'),
            'is_active' => true,
        ]);
    }
}
