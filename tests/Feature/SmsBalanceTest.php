<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SmsBalanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh');
        $this->seed(RoleSeeder::class);

        config([
            'services.senderge.base_url' => 'https://sender.ge/api',
            'services.senderge.apikey' => 'test-key',
        ]);
    }

    public function test_admin_can_retrieve_the_current_sms_balance(): void
    {
        Http::fake([
            'https://sender.ge/*' => Http::response([
                'data' => [[
                    'balance' => '42.50',
                    'overdraft' => '10.00',
                ]],
            ], 200),
        ]);

        $response = $this->actingAs($this->createUser('admin'))
            ->getJson(route('sms.balance'));

        $response
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'balance' => 42.5,
                'overdraft' => 10,
            ]);
        Http::assertSentCount(1);
    }

    public function test_balance_endpoint_returns_a_safe_error_when_sender_ge_is_unavailable(): void
    {
        Http::fake([
            'https://sender.ge/*' => Http::response(['message' => 'Unavailable'], 503),
        ]);

        $response = $this->actingAs($this->createUser('admin'))
            ->getJson(route('sms.balance'));

        $response
            ->assertStatus(503)
            ->assertJson([
                'ok' => false,
                'message' => 'SMS ბალანსის მიღება ვერ მოხერხდა.',
            ])
            ->assertJsonMissing(['raw', 'apikey']);
    }

    public function test_non_admin_cannot_retrieve_the_sms_balance(): void
    {
        $response = $this->actingAs($this->createUser('worker'))
            ->get(route('sms.balance'));

        $response->assertRedirect(route('admin.login.page'));
    }

    private function createUser(string $roleName): User
    {
        return User::create([
            'full_name' => ucfirst($roleName),
            'email' => "{$roleName}@example.test",
            'phone' => $roleName === 'admin' ? '500000001' : '500000002',
            'password' => 'secret',
            'role_id' => Role::query()->where('name', $roleName)->value('id'),
            'is_active' => true,
        ]);
    }
}
