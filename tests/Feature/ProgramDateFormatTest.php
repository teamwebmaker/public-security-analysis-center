<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProgramDateFormatTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh');
        $this->seed(RoleSeeder::class);
        Storage::fake('public');
        Carbon::setTestNow('2026-09-10 10:00:00');

        $this->admin = User::query()->create([
            'full_name' => 'Main Admin',
            'email' => 'admin@example.test',
            'phone' => '555000001',
            'password' => 'password',
            'role_id' => Role::query()->where('name', 'admin')->value('id'),
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_program_create_form_uses_day_month_year_inputs(): void
    {
        $this->actingAs($this->admin)
            ->get(route('programs.create'))
            ->assertOk()
            ->assertSee('type="text" id="start_date" name="start_date"', false)
            ->assertSee('type="text" id="end_date" name="end_date"', false)
            ->assertSee('placeholder="dd/mm/yyyy"', false)
            ->assertDontSee('type="date" id="start_date"', false);
    }

    public function test_program_dates_are_stored_from_day_month_year_format(): void
    {
        $this->actingAs($this->admin)
            ->post(route('programs.store'), $this->validPayload())
            ->assertRedirect(route('programs.index'));

        $program = Program::query()->firstOrFail();

        $this->assertSame('2026-09-20', $program->start_date);
        $this->assertSame('2026-09-25', $program->end_date);
    }

    public function test_program_create_rejects_month_day_year_input(): void
    {
        $this->actingAs($this->admin)
            ->post(route('programs.store'), $this->validPayload([
                'start_date' => '09/20/2026',
                'end_date' => '09/25/2026',
            ]))
            ->assertSessionHasErrors(['start_date', 'end_date']);

        $this->assertDatabaseCount('programs', 0);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title_ka' => 'სატესტო პროგრამა',
            'title_en' => 'Test Program',
            'description_ka' => 'სატესტო პროგრამის სრული აღწერა',
            'description_en' => 'Complete description of the test program',
            'image' => UploadedFile::fake()->image('program.jpg'),
            'certificate_image' => null,
            'video' => null,
            'price' => 100,
            'duration' => '5 დღე',
            'address' => 'თბილისი',
            'start_date' => '20/09/2026',
            'end_date' => '25/09/2026',
            'hour_start' => '10:00',
            'hour_end' => '12:00',
            'days' => ['ორშაბათი'],
            'mentor_ids' => [],
            'visibility' => '1',
        ], $overrides);
    }
}
