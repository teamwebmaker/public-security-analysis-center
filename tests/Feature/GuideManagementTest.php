<?php

namespace Tests\Feature;

use App\Models\Guide;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GuideManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_guides_table_has_expected_columns(): void
    {
        $this->assertTrue(Schema::hasTable('guides'));
        $this->assertTrue(Schema::hasColumns('guides', [
            'id',
            'name',
            'link',
            'sort_order',
            'created_at',
            'updated_at',
        ]));
    }

    public function test_admin_can_access_guide_index_create_and_edit_pages(): void
    {
        $admin = $this->createUserForRole('admin', 'admin@example.test', '500100100');
        $guide = Guide::create([
            'name' => 'Task Guide',
            'link' => '/admin/tasks/create',
            'sort_order' => 1,
        ]);

        $this->actingAs($admin)->get(route('guides.index'))->assertOk();
        $this->actingAs($admin)->get(route('guides.create'))->assertOk();
        $this->actingAs($admin)->get(route('guides.edit', $guide))->assertOk();
    }

    public function test_non_admin_users_cannot_access_guide_routes(): void
    {
        $worker = $this->createUserForRole('worker', 'worker@example.test', '500100101');
        $guide = Guide::create([
            'name' => 'Branch Guide',
            'link' => '/admin/branches/create',
            'sort_order' => 1,
        ]);

        $this->actingAs($worker)->get(route('guides.index'))->assertRedirect(route('admin.login.page'));
        $this->actingAs($worker)->get(route('guides.create'))->assertRedirect(route('admin.login.page'));
        $this->actingAs($worker)->get(route('guides.edit', $guide))->assertRedirect(route('admin.login.page'));
    }

    public function test_admin_can_store_internal_and_external_links(): void
    {
        $admin = $this->createUserForRole('admin', 'admin@example.test', '500100102');

        $this->actingAs($admin)->post(route('guides.store'), [
            'name' => 'Internal Guide',
            'link' => '/admin/tasks/create',
            'sort_order' => 1,
        ])->assertRedirect(route('guides.index'));

        $this->actingAs($admin)->post(route('guides.store'), [
            'name' => 'External Guide',
            'link' => 'https://example.com/guide',
            'sort_order' => 2,
        ])->assertRedirect(route('guides.index'));

        $this->assertDatabaseHas('guides', [
            'name' => 'Internal Guide',
            'link' => '/admin/tasks/create',
            'sort_order' => 1,
        ]);
        $this->assertDatabaseHas('guides', [
            'name' => 'External Guide',
            'link' => 'https://example.com/guide',
            'sort_order' => 2,
        ]);
    }

    public function test_store_validation_rejects_empty_invalid_and_duplicate_values(): void
    {
        $admin = $this->createUserForRole('admin', 'admin@example.test', '500100103');
        Guide::create([
            'name' => 'Existing Guide',
            'link' => '/admin/tasks',
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($admin)->from(route('guides.create'))->post(route('guides.store'), [
            'name' => '',
            'link' => 'not-a-link',
            'sort_order' => 1,
        ]);

        $response->assertRedirect(route('guides.create'));
        $response->assertSessionHasErrors(['name', 'link', 'sort_order']);
    }

    public function test_update_validation_rejects_duplicate_sort_order_and_invalid_link(): void
    {
        $admin = $this->createUserForRole('admin', 'admin@example.test', '500100104');
        $firstGuide = Guide::create([
            'name' => 'First Guide',
            'link' => '/admin/companies/create',
            'sort_order' => 1,
        ]);
        $secondGuide = Guide::create([
            'name' => 'Second Guide',
            'link' => '/admin/tasks/create',
            'sort_order' => 2,
        ]);

        $response = $this->actingAs($admin)->from(route('guides.edit', $secondGuide))->put(route('guides.update', $secondGuide), [
            'name' => 'Updated Guide',
            'link' => 'invalid-link',
            'sort_order' => $firstGuide->sort_order,
        ]);

        $response->assertRedirect(route('guides.edit', $secondGuide));
        $response->assertSessionHasErrors(['link', 'sort_order']);
    }

    public function test_index_page_lists_guides_in_sort_order(): void
    {
        $admin = $this->createUserForRole('admin', 'admin@example.test', '500100105');
        Guide::create([
            'name' => 'Second',
            'link' => '/admin/tasks',
            'sort_order' => 2,
        ]);
        Guide::create([
            'name' => 'First',
            'link' => '/admin/companies',
            'sort_order' => 1,
        ]);

        $this->actingAs($admin)
            ->get(route('guides.index'))
            ->assertOk()
            ->assertSeeInOrder(['First', 'Second']);
    }

    public function test_admin_dashboard_topbar_contains_guides_link(): void
    {
        $admin = $this->createUserForRole('admin', 'admin@example.test', '500100106');

        $this->actingAs($admin)
            ->get(route('admin.dashboard.page'))
            ->assertOk()
            ->assertSee(route('guides.index'), false);
    }

    private function createUserForRole(string $roleName, string $email, string $phone): User
    {
        return User::create([
            'full_name' => ucfirst(str_replace('_', ' ', $roleName)) . ' User',
            'email' => $email,
            'phone' => $phone,
            'password' => 'secret123',
            'role_id' => Role::query()->where('name', $roleName)->value('id'),
            'is_active' => true,
        ]);
    }
}
