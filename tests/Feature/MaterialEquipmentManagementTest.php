<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\MaterialEquipment;
use App\Models\Role;
use App\Models\Task;
use App\Models\TaskOccurrence;
use App\Models\TaskOccurrenceStatus;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TaskOccurrenceStatusSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MaterialEquipmentManagementTest extends TestCase
{
    private Company $company;

    private Company $otherCompany;

    private Branch $branch;

    private Branch $secondBranch;

    private Branch $otherBranch;

    private User $admin;

    private User $companyLeader;

    private User $responsiblePerson;

    private User $otherResponsiblePerson;

    private User $worker;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh');
        $this->seed([RoleSeeder::class, TaskOccurrenceStatusSeeder::class]);
        Storage::fake('local');

        $this->company = $this->createCompany('Visible Company', '100000001');
        $this->otherCompany = $this->createCompany('Other Company', '100000002');
        $this->branch = $this->createBranch($this->company, 'Branch Alpha');
        $this->secondBranch = $this->createBranch($this->company, 'Branch Beta');
        $this->otherBranch = $this->createBranch($this->otherCompany, 'Branch Gamma');

        $this->admin = $this->createUser('admin', 'Main Admin');
        $this->companyLeader = $this->createUser('company_leader', 'Company Leader');
        $this->responsiblePerson = $this->createUser('responsible_person', 'Responsible Person');
        $this->otherResponsiblePerson = $this->createUser('responsible_person', 'Other Responsible');
        $this->worker = $this->createUser('worker', 'Task Worker');

        $this->companyLeader->companies()->attach($this->company);
        $this->responsiblePerson->branches()->attach($this->branch);
        $this->otherResponsiblePerson->branches()->attach($this->otherBranch);
    }

    public function test_admin_company_leader_and_responsible_person_can_create_signed_records(): void
    {
        $this->actingAs($this->admin)
            ->post(route('material-equipments.store'), $this->payload(
                $this->branch,
                [$this->companyLeader, $this->responsiblePerson],
                'Admin Equipment',
                MaterialEquipment::VISIBILITY_PUBLIC
            ))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->actingAs($this->companyLeader)
            ->post(route('management.material-equipments.store'), $this->payload(
                $this->secondBranch,
                [$this->companyLeader],
                'Leader Equipment'
            ))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->actingAs($this->responsiblePerson)
            ->post(route('management.material-equipments.store'), $this->payload(
                $this->branch,
                [$this->responsiblePerson],
                'Responsible Equipment'
            ))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseCount('material_equipments', 3);
        $this->assertDatabaseCount('material_equipment_signatures', 4);
        $this->assertDatabaseMissing('material_equipment_signatures', ['signed_at' => null]);

        $publicEquipment = MaterialEquipment::query()
            ->where('title', 'Admin Equipment')
            ->with('publicShare')
            ->firstOrFail();

        $this->assertNotNull($publicEquipment->publicShare);
        Storage::disk('local')->assertExists($publicEquipment->document_path);

        $this->get(route('public-shares.show', $publicEquipment->publicShare->token))
            ->assertOk()
            ->assertSee('Admin Equipment');
        $this->get(route('public-shares.document', $publicEquipment->publicShare->token))
            ->assertOk();
    }

    public function test_worker_can_view_only_active_task_branch_records_and_cannot_create(): void
    {
        $visible = $this->createMaterialEquipment($this->branch, 'Active Branch Equipment');
        $hidden = $this->createMaterialEquipment($this->otherBranch, 'Other Branch Equipment');
        $occurrence = $this->createAssignedTask($this->worker, $this->branch, 'pending');

        $this->actingAs($this->worker)
            ->get(route('management.material-equipments.index'))
            ->assertOk()
            ->assertSee($visible->title)
            ->assertDontSee($hidden->title);

        $this->actingAs($this->worker)
            ->get(route('management.material-equipments.show', $visible))
            ->assertOk()
            ->assertSee($visible->document_original_name);

        $this->actingAs($this->worker)
            ->get(route('management.material-equipments.create'))
            ->assertForbidden();

        $this->actingAs($this->worker)
            ->post(route('management.material-equipments.store'), $this->payload(
                $this->branch,
                [$this->responsiblePerson]
            ))
            ->assertForbidden();

        $occurrence->update([
            'status_id' => TaskOccurrenceStatus::query()->where('name', 'completed')->value('id'),
        ]);

        $this->actingAs($this->worker)
            ->get(route('management.material-equipments.index'))
            ->assertOk()
            ->assertDontSee($visible->title);

        $this->actingAs($this->worker)
            ->get(route('management.material-equipments.show', $visible))
            ->assertForbidden();
    }

    public function test_company_leader_and_responsible_person_visibility_is_connection_scoped(): void
    {
        $connected = $this->createMaterialEquipment($this->branch, 'Connected Equipment');
        $other = $this->createMaterialEquipment($this->otherBranch, 'Unrelated Equipment');

        $this->actingAs($this->companyLeader)
            ->get(route('management.material-equipments.index'))
            ->assertOk()
            ->assertSee($connected->title)
            ->assertDontSee($other->title);

        $this->actingAs($this->responsiblePerson)
            ->get(route('management.material-equipments.index'))
            ->assertOk()
            ->assertSee($connected->title)
            ->assertDontSee($other->title);

        $this->actingAs($this->responsiblePerson)
            ->get(route('management.material-equipments.show', $other))
            ->assertForbidden();
    }

    public function test_branch_and_signers_are_validated_against_creator_scope(): void
    {
        $this->actingAs($this->responsiblePerson)
            ->post(route('management.material-equipments.store'), $this->payload(
                $this->otherBranch,
                [$this->otherResponsiblePerson]
            ))
            ->assertSessionHasErrors('branch_id');

        $this->actingAs($this->responsiblePerson)
            ->post(route('management.material-equipments.store'), $this->payload(
                $this->branch,
                [$this->otherResponsiblePerson]
            ))
            ->assertSessionHasErrors('signer_user_ids');

        $this->assertDatabaseCount('material_equipments', 0);
    }

    public function test_only_creator_or_admin_can_manage_record_and_public_link_can_be_revoked(): void
    {
        $materialEquipment = $this->createMaterialEquipment(
            $this->branch,
            'Creator Equipment',
            $this->responsiblePerson
        );
        $secondResponsible = $this->createUser('responsible_person', 'Second Responsible');
        $secondResponsible->branches()->attach($this->branch);

        $this->actingAs($secondResponsible)
            ->get(route('management.material-equipments.edit', $materialEquipment))
            ->assertForbidden();

        $this->actingAs($secondResponsible)
            ->delete(route('management.material-equipments.destroy', $materialEquipment))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->put(route('material-equipments.update', $materialEquipment), [
                'title' => 'Public Equipment',
                'branch_id' => $this->branch->id,
                'document_visibility' => MaterialEquipment::VISIBILITY_PUBLIC,
                'signer_user_ids' => [$this->responsiblePerson->id],
            ])
            ->assertRedirect(route('material-equipments.show', $materialEquipment));

        $materialEquipment->refresh()->load('publicShare');
        $shareToken = $materialEquipment->publicShare->token;
        $this->get(route('public-shares.show', $shareToken))->assertOk();

        $this->actingAs($this->admin)
            ->put(route('material-equipments.update', $materialEquipment), [
                'title' => 'Private Equipment',
                'branch_id' => $this->branch->id,
                'document_visibility' => MaterialEquipment::VISIBILITY_PRIVATE,
                'signer_user_ids' => [$this->responsiblePerson->id],
            ])
            ->assertRedirect(route('material-equipments.show', $materialEquipment));

        $this->get(route('public-shares.show', $shareToken))->assertNotFound();
    }

    public function test_signed_document_and_branch_cannot_be_replaced_during_update(): void
    {
        $materialEquipment = $this->createMaterialEquipment($this->branch, 'Immutable Equipment');

        $this->actingAs($this->admin)
            ->put(route('material-equipments.update', $materialEquipment), [
                'title' => 'Changed Equipment',
                'branch_id' => $this->otherBranch->id,
                'document' => UploadedFile::fake()->create('replacement.pdf', 20, 'application/pdf'),
                'document_visibility' => MaterialEquipment::VISIBILITY_PRIVATE,
                'signer_user_ids' => [$this->responsiblePerson->id],
            ])
            ->assertSessionHasErrors(['branch_id', 'document']);

        $this->assertDatabaseHas('material_equipments', [
            'id' => $materialEquipment->id,
            'title' => 'Immutable Equipment',
            'branch_id' => $this->branch->id,
        ]);
    }

    public function test_migration_repairs_indexes_after_a_partial_failure(): void
    {
        $uniqueIndex = 'material_equipment_signatures_equipment_user_unique';
        $signedIndex = 'material_equipment_signatures_user_signed_index';

        Schema::table('material_equipment_signatures', function ($table) use ($uniqueIndex, $signedIndex) {
            $table->dropUnique($uniqueIndex);
            $table->dropIndex($signedIndex);
        });

        $this->assertFalse(Schema::hasIndex('material_equipment_signatures', $uniqueIndex));
        $this->assertFalse(Schema::hasIndex('material_equipment_signatures', $signedIndex));

        $migration = require database_path(
            'migrations/2026_08_16_000000_create_material_equipments_tables.php'
        );
        $migration->up();

        $this->assertTrue(Schema::hasIndex('material_equipment_signatures', $uniqueIndex));
        $this->assertTrue(Schema::hasIndex('material_equipment_signatures', $signedIndex));
    }

    private function payload(
        Branch $branch,
        array $signers,
        string $title = 'Test Equipment',
        string $visibility = MaterialEquipment::VISIBILITY_PRIVATE
    ): array {
        return [
            'title' => $title,
            'branch_id' => $branch->id,
            'document' => UploadedFile::fake()->create('equipment.pdf', 100, 'application/pdf'),
            'document_visibility' => $visibility,
            'signer_user_ids' => collect($signers)->pluck('id')->all(),
        ];
    }

    private function createMaterialEquipment(
        Branch $branch,
        string $title,
        ?User $creator = null
    ): MaterialEquipment {
        $path = 'material-equipments/'.uniqid().'.pdf';
        Storage::disk('local')->put($path, 'equipment document');

        $materialEquipment = MaterialEquipment::create([
            'title' => $title,
            'branch_id' => $branch->id,
            'created_by_user_id' => ($creator ?? $this->admin)->id,
            'document_path' => $path,
            'document_original_name' => "{$title}.pdf",
            'document_mime_type' => 'application/pdf',
            'document_visibility' => MaterialEquipment::VISIBILITY_PRIVATE,
        ]);

        $signer = $branch->is($this->otherBranch)
            ? $this->otherResponsiblePerson
            : $this->responsiblePerson;
        $materialEquipment->signatures()->create([
            'user_id' => $signer->id,
            'signed_at' => now(),
        ]);

        return $materialEquipment;
    }

    private function createAssignedTask(User $worker, Branch $branch, string $status): TaskOccurrence
    {
        $task = Task::create([
            'created_by_user_id' => $this->admin->id,
            'branch_id' => $branch->id,
            'branch_name_snapshot' => $branch->name,
            'service_id' => null,
            'service_name_snapshot' => 'Temporary Service',
            'recurrence_interval' => null,
            'is_recurring' => false,
            'archived' => false,
            'visibility' => '1',
        ]);
        $task->users()->attach($worker);

        return TaskOccurrence::create([
            'task_id' => $task->id,
            'branch_id_snapshot' => $branch->id,
            'branch_name_snapshot' => $branch->name,
            'service_id_snapshot' => null,
            'service_name_snapshot' => 'Temporary Service',
            'status_id' => TaskOccurrenceStatus::query()->where('name', $status)->value('id'),
            'requires_document' => false,
            'payment_status' => 'unpaid',
            'visibility' => '1',
        ]);
    }

    private function createCompany(string $name, string $code): Company
    {
        return Company::create([
            'name' => $name,
            'economic_activity_type_id' => null,
            'identification_code' => $code,
            'economic_activity_code' => '123',
            'high_risk_activities' => false,
            'risk_level' => 'low',
            'evacuation_plan' => true,
            'visibility' => '1',
        ]);
    }

    private function createBranch(Company $company, string $name): Branch
    {
        return Branch::create([
            'name' => $name,
            'address' => 'Tbilisi',
            'company_id' => $company->id,
            'visibility' => '1',
        ]);
    }

    private function createUser(string $role, string $name): User
    {
        return User::create([
            'full_name' => $name,
            'email' => strtolower(str_replace(' ', '-', $name)).'-'.uniqid().'@example.test',
            'phone' => '5'.random_int(10000000, 99999999),
            'password' => 'secret',
            'role_id' => Role::query()->where('name', $role)->value('id'),
            'is_active' => true,
        ]);
    }
}
