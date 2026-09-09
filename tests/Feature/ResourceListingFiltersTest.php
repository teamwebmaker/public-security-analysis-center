<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\DocumentTemplate;
use App\Models\Incident;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ResourceListingFiltersTest extends TestCase
{
    private User $admin;

    private User $worker;

    private User $otherWorker;

    private User $responsiblePerson;

    private Branch $allowedBranch;

    private Branch $otherBranch;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh');
        $this->seed(RoleSeeder::class);

        $this->admin = $this->createUser('admin', 'Main Admin');
        $this->worker = $this->createUser('worker', 'Assigned Worker');
        $this->otherWorker = $this->createUser('worker', 'Other Worker');
        $this->responsiblePerson = $this->createUser('responsible_person', 'Responsible Person');
        $this->allowedBranch = $this->createBranch('Allowed Company', 'Allowed Branch', 'FILTER-001');
        $this->otherBranch = $this->createBranch('Restricted Company', 'Restricted Branch', 'FILTER-002');
    }

    public function test_admin_can_combine_incident_search_and_filters(): void
    {
        $matching = $this->createIncident(
            'Target Incident',
            $this->allowedBranch,
            $this->worker,
            'public',
            $this->responsiblePerson
        );
        $other = $this->createIncident(
            'Other Incident',
            $this->otherBranch,
            $this->otherWorker,
            'private',
            $this->responsiblePerson,
            now()
        );

        $this->actingAs($this->admin)
            ->get(route('incidents.index', [
                'search' => 'Target',
                'company_id' => $this->allowedBranch->company_id,
                'branch_id' => $this->allowedBranch->id,
                'document_visibility' => 'public',
                'signature_status' => 'pending',
            ]))
            ->assertOk()
            ->assertSee($matching->title)
            ->assertDontSee($other->title)
            ->assertSee('name="company_id"', false)
            ->assertSee('name="branch_id"', false)
            ->assertSee('name="signature_status"', false);
    }

    public function test_incident_filters_preserve_responsible_person_scope_and_option_privacy(): void
    {
        $visible = $this->createIncident(
            'Visible Incident',
            $this->allowedBranch,
            $this->worker,
            'private',
            $this->responsiblePerson
        );
        $hidden = $this->createIncident(
            'Hidden Incident',
            $this->otherBranch,
            $this->otherWorker,
            'private',
            $this->otherWorker
        );

        $this->actingAs($this->responsiblePerson)
            ->get(route('management.incidents.index'))
            ->assertOk()
            ->assertSee($visible->title)
            ->assertDontSee($hidden->title)
            ->assertSee($this->allowedBranch->name)
            ->assertDontSee($this->otherBranch->name)
            ->assertDontSee($this->otherBranch->company->name);

        $this->actingAs($this->responsiblePerson)
            ->get(route('management.incidents.index', ['branch_id' => $this->otherBranch->id]))
            ->assertOk()
            ->assertDontSee($hidden->title);
    }

    public function test_order_search_and_filters_remain_participant_scoped(): void
    {
        $matching = $this->createOrder(
            'Target Order',
            $this->allowedBranch,
            $this->worker,
            'public',
            $this->responsiblePerson
        );
        $hidden = $this->createOrder(
            'Hidden Order',
            $this->otherBranch,
            $this->otherWorker,
            'public',
            $this->otherWorker
        );

        $this->actingAs($this->responsiblePerson)
            ->get(route('management.orders.index', [
                'search' => 'Target',
                'company_id' => $this->allowedBranch->company_id,
                'branch_id' => $this->allowedBranch->id,
                'document_visibility' => 'public',
                'signature_status' => 'pending',
            ]))
            ->assertOk()
            ->assertSee($matching->title)
            ->assertDontSee($hidden->title)
            ->assertDontSee($this->otherBranch->name)
            ->assertSee('name="document_visibility"', false);
    }

    public function test_document_template_filters_work_for_admin_and_assigned_worker(): void
    {
        $matching = $this->createDocumentTemplate(
            'Target PDF Template',
            'documents/document-templates/target.pdf',
            '1',
            [$this->worker]
        );
        $other = $this->createDocumentTemplate(
            'Other Word Template',
            'documents/document-templates/other.docx',
            '0',
            [$this->otherWorker]
        );
        $unassigned = $this->createDocumentTemplate(
            'Target Unassigned PDF',
            'documents/document-templates/unassigned.pdf',
            '1',
            [$this->otherWorker]
        );

        $this->actingAs($this->admin)
            ->get(route('document-templates.index', [
                'search' => 'Target PDF',
                'worker_id' => $this->worker->id,
                'visibility' => '1',
                'document_type' => 'pdf',
            ]))
            ->assertOk()
            ->assertSee($matching->name)
            ->assertDontSee($other->name)
            ->assertDontSee($unassigned->name)
            ->assertSee('name="worker_id"', false)
            ->assertSee('name="visibility"', false);

        $this->actingAs($this->worker)
            ->get(route('management.worker.document-templates.page', [
                'search' => 'Target',
                'document_type' => 'pdf',
            ]))
            ->assertOk()
            ->assertSee($matching->name)
            ->assertDontSee($other->name)
            ->assertDontSee($unassigned->name)
            ->assertSee('name="document_type"', false);
    }

    private function createIncident(
        string $title,
        Branch $branch,
        User $creator,
        string $documentVisibility,
        User $participant,
        $signedAt = null
    ): Incident {
        $incident = Incident::query()->create([
            'title' => $title,
            'branch_id' => $branch->id,
            'created_by_user_id' => $creator->id,
            'document_path' => "incidents/{$title}.pdf",
            'document_original_name' => "{$title}.pdf",
            'document_mime_type' => 'application/pdf',
            'document_visibility' => $documentVisibility,
        ]);
        $incident->userParticipants()->create([
            'user_id' => $participant->id,
            'signed_at' => $signedAt,
        ]);

        return $incident;
    }

    private function createOrder(
        string $title,
        Branch $branch,
        User $creator,
        string $documentVisibility,
        User $participant
    ): Order {
        $order = Order::query()->create([
            'title' => $title,
            'branch_id' => $branch->id,
            'created_by_user_id' => $creator->id,
            'document_path' => "orders/{$title}.pdf",
            'document_original_name' => "{$title}.pdf",
            'document_mime_type' => 'application/pdf',
            'document_visibility' => $documentVisibility,
        ]);
        $order->userParticipants()->create([
            'user_id' => $participant->id,
            'signed_at' => null,
        ]);

        return $order;
    }

    /**
     * @param  array<int, User>  $workers
     */
    private function createDocumentTemplate(
        string $name,
        string $document,
        string $visibility,
        array $workers
    ): DocumentTemplate {
        $template = DocumentTemplate::query()->create([
            'name' => $name,
            'document' => $document,
            'visibility' => $visibility,
        ]);
        $template->users()->attach(collect($workers)->pluck('id'));

        return $template;
    }

    private function createBranch(string $companyName, string $branchName, string $identificationCode): Branch
    {
        $company = Company::query()->create([
            'name' => $companyName,
            'economic_activity_type_id' => null,
            'identification_code' => $identificationCode,
            'economic_activity_code' => '123456',
            'high_risk_activities' => false,
            'risk_level' => 'low',
            'evacuation_plan' => true,
            'visibility' => '1',
        ]);

        return Branch::query()->create([
            'name' => $branchName,
            'address' => "{$branchName} Address",
            'company_id' => $company->id,
            'visibility' => '1',
        ]);
    }

    private function createUser(string $roleName, string $fullName): User
    {
        return User::query()->create([
            'full_name' => $fullName,
            'email' => strtolower(str_replace(' ', '.', $fullName)).'@example.test',
            'phone' => (string) random_int(500000000, 599999999),
            'password' => 'password',
            'role_id' => Role::query()->where('name', $roleName)->value('id'),
            'is_active' => true,
        ]);
    }
}
