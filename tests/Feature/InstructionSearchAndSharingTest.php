<?php

namespace Tests\Feature;

use App\Models\Instruction;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InstructionSearchAndSharingTest extends TestCase
{
    private User $admin;

    private User $worker;

    private User $otherWorker;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh');
        $this->seed(RoleSeeder::class);
        Storage::fake('local');
        Storage::fake('public');

        $this->admin = $this->createUser('admin', 'Main Admin');
        $this->worker = $this->createUser('worker', 'Assigned Worker');
        $this->otherWorker = $this->createUser('worker', 'Other Worker');
    }

    public function test_admin_can_create_public_instruction_with_secure_share_link(): void
    {
        $this->actingAs($this->admin)
            ->post(route('instructions.store'), $this->instructionPayload([
                'name' => 'Public Safety Manual',
                'document_visibility' => 'public',
                'worker_ids' => [$this->worker->id],
            ]))
            ->assertRedirect(route('instructions.index'));

        $instruction = Instruction::query()->firstOrFail();
        $share = $instruction->publicShare()->firstOrFail();

        Storage::disk('local')->assertExists($instruction->document);
        Storage::disk('public')->assertMissing($instruction->document);
        $this->assertTrue($share->isUsable());

        $this->get(route('public-shares.show', $share->token))
            ->assertOk()
            ->assertSee('Public Safety Manual');

        $this->get(route('public-shares.document', $share->token))
            ->assertOk()
            ->assertDownload('manual.pdf');
    }

    public function test_migration_moves_legacy_instruction_documents_out_of_public_storage(): void
    {
        $migration = 'database/migrations/2026_08_13_000000_add_sharing_to_instructions_table.php';
        Artisan::call('migrate:rollback', ['--path' => $migration, '--force' => true]);

        $legacyPath = 'documents/instructions/legacy-manual.pdf';
        Storage::disk('public')->put($legacyPath, 'legacy document');
        $instructionId = DB::table('instructions')->insertGetId([
            'name' => 'Legacy Manual',
            'link' => null,
            'document' => $legacyPath,
            'visibility' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Artisan::call('migrate', ['--path' => $migration, '--force' => true]);

        $instruction = Instruction::query()->findOrFail($instructionId);
        $this->assertSame('private', $instruction->document_visibility);
        $this->assertSame('legacy-manual.pdf', $instruction->document_original_name);
        Storage::disk('local')->assertExists($instruction->document);
        Storage::disk('public')->assertMissing($legacyPath);
    }

    public function test_admin_search_and_filters_can_be_combined(): void
    {
        $matching = $this->createInstruction(
            'Fire Safety Guide',
            'fire-guide.pdf',
            'public',
            '1',
            [$this->worker]
        );
        $other = $this->createInstruction(
            'Office Safety Guide',
            'office-guide.docx',
            'private',
            '0',
            [$this->otherWorker]
        );

        $this->actingAs($this->admin)
            ->get(route('instructions.index', [
                'search' => 'Fire',
                'worker_id' => $this->worker->id,
                'visibility' => '1',
                'document_visibility' => 'public',
                'document_type' => 'pdf',
            ]))
            ->assertOk()
            ->assertSee($matching->name)
            ->assertDontSee($other->name)
            ->assertSee('value="Fire"', false)
            ->assertSee(route('public-shares.show', $matching->publicShare->token));
    }

    public function test_worker_search_and_filters_remain_assignment_scoped(): void
    {
        $matching = $this->createInstruction(
            'Assigned Public PDF',
            'assigned.pdf',
            'public',
            '1',
            [$this->worker]
        );
        $unassigned = $this->createInstruction(
            'Unassigned Public PDF',
            'unassigned.pdf',
            'public',
            '1',
            [$this->otherWorker]
        );
        $hidden = $this->createInstruction(
            'Hidden Assigned PDF',
            'hidden.pdf',
            'public',
            '0',
            [$this->worker]
        );

        $this->actingAs($this->worker)
            ->get(route('management.worker.instructions.page', [
                'search' => 'Assigned',
                'document_visibility' => 'public',
                'document_type' => 'pdf',
            ]))
            ->assertOk()
            ->assertSee($matching->name)
            ->assertDontSee($unassigned->name)
            ->assertDontSee($hidden->name);

        $this->actingAs($this->worker)
            ->get(route('management.worker.instructions.document', $matching))
            ->assertOk();

        $this->actingAs($this->worker)
            ->get(route('management.worker.instructions.document', $unassigned))
            ->assertForbidden();

        $sharedAssignment = $this->createInstruction(
            'Shared Team Manual',
            'shared-team.pdf',
            'private',
            '1',
            [$this->worker, $this->otherWorker]
        );

        $this->actingAs($this->worker)
            ->get(route('management.worker.instructions.page', [
                'search' => $this->otherWorker->full_name,
            ]))
            ->assertOk()
            ->assertDontSee($sharedAssignment->name);
    }

    public function test_switching_instruction_to_private_revokes_public_link(): void
    {
        $instruction = $this->createInstruction(
            'Shared Instruction',
            'shared.pdf',
            'public',
            '1',
            [$this->worker]
        );
        $share = app(\App\Services\PublicSharing\PublicShareService::class)
            ->enable($instruction, $this->admin);

        $payload = [
            'name' => $instruction->name,
            'link' => $instruction->link,
            'worker_ids' => [$this->worker->id],
            'visibility' => '1',
            'document_visibility' => 'private',
        ];

        $this->actingAs($this->admin)
            ->put(route('instructions.update', $instruction), $payload)
            ->assertRedirect();

        $this->assertSame('private', $instruction->fresh()->document_visibility);
        $this->assertDatabaseHas('public_shares', [
            'id' => $share->id,
            'is_active' => false,
        ]);

        $this->get(route('public-shares.show', $share->token))->assertNotFound();

        $this->actingAs($this->worker)
            ->get(route('management.worker.instructions.document', $instruction))
            ->assertOk();
    }

    public function test_public_link_is_controlled_by_document_sharing_not_worker_visibility(): void
    {
        $instruction = $this->createInstruction(
            'Hidden Public Instruction',
            'hidden-public.pdf',
            'public',
            '0',
            [$this->worker]
        );

        $this->get(route('public-shares.show', $instruction->publicShare->token))
            ->assertOk()
            ->assertSee($instruction->name);

        $this->actingAs($this->worker)
            ->get(route('management.worker.instructions.document', $instruction))
            ->assertNotFound();
    }

    public function test_replacing_document_removes_old_protected_file(): void
    {
        $instruction = $this->createInstruction(
            'Replace Document',
            'old.pdf',
            'private',
            '1',
            [$this->worker]
        );
        $oldPath = $instruction->document;

        $payload = [
            'name' => $instruction->name,
            'link' => null,
            'document' => UploadedFile::fake()->create('new.pdf', 80, 'application/pdf'),
            'worker_ids' => [$this->worker->id],
            'visibility' => '1',
            'document_visibility' => 'private',
        ];

        $this->actingAs($this->admin)
            ->put(route('instructions.update', $instruction), $payload)
            ->assertRedirect();

        Storage::disk('local')->assertMissing($oldPath);
        Storage::disk('local')->assertExists($instruction->fresh()->document);
    }

    private function instructionPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Safety Instruction',
            'link' => 'https://example.com/video',
            'document' => UploadedFile::fake()->create('manual.pdf', 100, 'application/pdf'),
            'worker_ids' => [],
            'visibility' => '1',
            'document_visibility' => 'private',
        ], $overrides);
    }

    private function createInstruction(
        string $name,
        string $documentName,
        string $documentVisibility,
        string $visibility,
        array $workers
    ): Instruction {
        $path = 'instructions/'.uniqid().'-'.$documentName;
        Storage::disk('local')->put($path, 'document contents');

        $instruction = Instruction::create([
            'name' => $name,
            'link' => 'https://example.com/'.str($name)->slug(),
            'document' => $path,
            'document_original_name' => $documentName,
            'document_mime_type' => str_ends_with($documentName, '.pdf')
                ? 'application/pdf'
                : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'document_visibility' => $documentVisibility,
            'visibility' => $visibility,
        ]);
        $instruction->users()->attach(collect($workers)->pluck('id'));

        if ($instruction->isPublic()) {
            app(\App\Services\PublicSharing\PublicShareService::class)
                ->enable($instruction, $this->admin);
            $instruction->load('publicShare');
        }

        return $instruction;
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
