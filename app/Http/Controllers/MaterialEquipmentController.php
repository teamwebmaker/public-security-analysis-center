<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialEquipmentRequest;
use App\Http\Requests\UpdateMaterialEquipmentRequest;
use App\Models\MaterialEquipment;
use App\Services\Incidents\IncidentParticipantOptions;
use App\Services\MaterialEquipment\MaterialEquipmentBranchAccess;
use App\Services\MaterialEquipment\MaterialEquipmentManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Throwable;

class MaterialEquipmentController extends Controller
{
    public function __construct(
        private MaterialEquipmentManager $manager,
        private MaterialEquipmentBranchAccess $branchAccess,
        private IncidentParticipantOptions $participantOptions
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', MaterialEquipment::class);
        $user = $request->user();
        $search = trim((string) $request->query('search'));

        $materialEquipments = MaterialEquipment::query()
            ->visibleTo($user)
            ->with([
                'branch.company:id,name',
                'creator:id,full_name',
                'signatures.user.role',
                'publicShare',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $like = "%{$search}%";
                $query->where(function ($query) use ($like) {
                    $query->where('title', 'LIKE', $like)
                        ->orWhereHas('branch', fn ($branch) => $branch->where('name', 'LIKE', $like))
                        ->orWhereHas('branch.company', fn ($company) => $company->where('name', 'LIKE', $like))
                        ->orWhereHas(
                            'signatures.user',
                            fn ($user) => $user->where('full_name', 'LIKE', $like)
                        );
                });
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view($user->isAdmin() ? 'admin.material-equipments.index' : 'management.material-equipments.index', [
            'materialEquipments' => $materialEquipments,
            'sidebarItems' => $user->isAdmin() ? null : $this->managementSidebar($user),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', MaterialEquipment::class);
        $branches = $this->branchAccess->forUser($request->user());

        return view($request->user()->isAdmin()
            ? 'admin.material-equipments.create'
            : 'management.material-equipments.create', [
                'branches' => $branches,
                'participantOptions' => $this->participantOptions->forBranches($branches),
                'sidebarItems' => $request->user()->isAdmin() ? null : $this->managementSidebar($request->user()),
            ]);
    }

    public function store(StoreMaterialEquipmentRequest $request): RedirectResponse
    {
        try {
            $materialEquipment = $this->manager->create($request->validated(), $request->user());
        } catch (Throwable $e) {
            Log::error('Material equipment creation failed', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'material_equipment' => 'მატერიალურ-ტექნიკური აღჭურვილობის შექმნა ვერ მოხერხდა.',
            ]);
        }

        return redirect()
            ->route($this->routeName($request->user(), 'show'), $materialEquipment)
            ->with('success', 'მატერიალურ-ტექნიკური აღჭურვილობა წარმატებით შეიქმნა.');
    }

    public function show(Request $request, MaterialEquipment $materialEquipment): View
    {
        $this->authorize('view', $materialEquipment);
        $materialEquipment->load([
            'branch.company:id,name',
            'creator:id,full_name',
            'signatures.user.role',
            'publicShare',
        ]);

        return view($request->user()->isAdmin()
            ? 'admin.material-equipments.show'
            : 'management.material-equipments.show', [
                'materialEquipment' => $materialEquipment,
                'sidebarItems' => $request->user()->isAdmin() ? null : $this->managementSidebar($request->user()),
            ]);
    }

    public function edit(Request $request, MaterialEquipment $materialEquipment): View
    {
        $this->authorize('update', $materialEquipment);
        $materialEquipment->load(['branch.company:id,name', 'signatures.user.role']);
        $branches = $this->branchAccess->forUser($request->user(), $materialEquipment);
        $participantOptions = $this->participantOptions->forBranches($branches);
        $knownIds = collect($participantOptions[$materialEquipment->branch_id] ?? [])->pluck('id');

        foreach ($materialEquipment->signatures as $signature) {
            if ($knownIds->contains($signature->user_id)) {
                continue;
            }

            $participantOptions[$materialEquipment->branch_id][] = [
                'id' => $signature->user_id,
                'name' => $signature->user->full_name,
                'role' => $signature->user->getRoleName(),
                'role_label' => $signature->user->getRoleName() === 'company_leader'
                    ? 'კომპანიის ხელმძღვანელი'
                    : 'პასუხისმგებელი პირი',
            ];
        }

        return view($request->user()->isAdmin()
            ? 'admin.material-equipments.edit'
            : 'management.material-equipments.edit', [
                'materialEquipment' => $materialEquipment,
                'branches' => $branches,
                'participantOptions' => $participantOptions,
                'sidebarItems' => $request->user()->isAdmin() ? null : $this->managementSidebar($request->user()),
            ]);
    }

    public function update(
        UpdateMaterialEquipmentRequest $request,
        MaterialEquipment $materialEquipment
    ): RedirectResponse {
        try {
            $materialEquipment = $this->manager->update(
                $materialEquipment,
                $request->validated(),
                $request->user()
            );
        } catch (Throwable $e) {
            Log::error('Material equipment update failed', [
                'material_equipment_id' => $materialEquipment->id,
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'material_equipment' => 'მატერიალურ-ტექნიკური აღჭურვილობის განახლება ვერ მოხერხდა.',
            ]);
        }

        return redirect()
            ->route($this->routeName($request->user(), 'show'), $materialEquipment)
            ->with('success', 'მატერიალურ-ტექნიკური აღჭურვილობა წარმატებით განახლდა.');
    }

    public function destroy(Request $request, MaterialEquipment $materialEquipment): RedirectResponse
    {
        $this->authorize('delete', $materialEquipment);

        try {
            $this->manager->delete($materialEquipment);
        } catch (Throwable $e) {
            Log::error('Material equipment deletion failed', [
                'material_equipment_id' => $materialEquipment->id,
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'material_equipment' => 'მატერიალურ-ტექნიკური აღჭურვილობის წაშლა ვერ მოხერხდა.',
            ]);
        }

        return redirect()
            ->route($this->routeName($request->user(), 'index'))
            ->with('success', 'მატერიალურ-ტექნიკური აღჭურვილობა წარმატებით წაიშალა.');
    }

    public function document(MaterialEquipment $materialEquipment): BinaryFileResponse
    {
        $this->authorize('view', $materialEquipment);
        abort_unless(Storage::disk('local')->exists($materialEquipment->document_path), 404);

        $response = response()->file(
            Storage::disk('local')->path($materialEquipment->document_path),
            [
                'Content-Type' => $materialEquipment->document_mime_type ?: 'application/octet-stream',
                'Cache-Control' => 'private, no-store',
            ]
        );
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $materialEquipment->document_original_name
        );

        return $response;
    }

    public function downloadDocument(MaterialEquipment $materialEquipment): BinaryFileResponse
    {
        $this->authorize('view', $materialEquipment);
        abort_unless(Storage::disk('local')->exists($materialEquipment->document_path), 404);

        return response()->download(
            Storage::disk('local')->path($materialEquipment->document_path),
            $materialEquipment->document_original_name,
            [
                'Content-Type' => $materialEquipment->document_mime_type ?: 'application/octet-stream',
                'Cache-Control' => 'private, no-store',
            ]
        );
    }

    private function routeName($user, string $action): string
    {
        return $user->isAdmin()
            ? "material-equipments.{$action}"
            : "management.material-equipments.{$action}";
    }

    private function managementSidebar($user): array
    {
        return config('sidebar.'.str_replace('_', '-', $user->getRoleName()), []);
    }
}
