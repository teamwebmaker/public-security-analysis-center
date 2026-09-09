<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Models\Branch;
use App\Models\Incident;
use App\Models\IncidentExternalParticipant;
use App\Services\Incidents\IncidentManager;
use App\Services\Incidents\IncidentParticipantOptions;
use App\Services\Resources\BranchResourceFilterService;
use App\Services\Sms\IncidentSmsNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Throwable;

class IncidentController extends Controller
{
    public function __construct(
        private IncidentManager $incidentManager,
        private IncidentParticipantOptions $participantOptions,
        private IncidentSmsNotifier $smsNotifier,
        private BranchResourceFilterService $filters
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Incident::class);
        $user = $request->user();

        $query = Incident::query()->visibleTo($user);
        $filterOptions = $this->filters->options(clone $query);
        $this->filters->apply(
            $query,
            $request,
            [
                'userParticipants.user' => 'full_name',
                'externalParticipants' => 'full_name',
            ],
            ['userParticipants', 'externalParticipants']
        );

        $incidents = $query
            ->with([
                'branch.company',
                'creator:id,full_name',
                'userParticipants.user.role',
                'externalParticipants.signedMarkedBy:id,full_name',
                'publicShare',
            ])
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view($user->isAdmin() ? 'admin.incidents.index' : 'management.incidents.index', [
            'incidents' => $incidents,
            'filterOptions' => $filterOptions,
            'sidebarItems' => $user->isAdmin() ? null : $this->managementSidebar($user->getRoleName()),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Incident::class);
        $branches = Branch::query()
            ->where('visibility', '1')
            ->with('company:id,name')
            ->orderBy('name')
            ->get();

        return view('management.incidents.create', [
            'branches' => $branches,
            'participantOptions' => $this->participantOptions->forBranches($branches),
            'sidebarItems' => config('sidebar.worker'),
        ]);
    }

    public function store(StoreIncidentRequest $request): RedirectResponse
    {
        try {
            $result = $this->incidentManager->create($request->validated(), $request->user());
            $this->smsNotifier->notifyCreated(
                $result['incident'],
                $result['new_user_participants'],
                $result['new_external_participants']
            );
        } catch (Throwable $e) {
            Log::error('Incident creation failed', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'incident' => 'ინციდენტის შექმნა ვერ მოხერხდა. გთხოვთ, სცადოთ თავიდან.',
            ]);
        }

        return redirect()
            ->route('management.incidents.show', $result['incident'])
            ->with('success', 'ინციდენტი წარმატებით შეიქმნა.');
    }

    public function show(Request $request, Incident $incident): View
    {
        $this->authorize('view', $incident);
        $incident->load([
            'branch.company',
            'creator:id,full_name',
            'userParticipants.user.role',
            'externalParticipants.signedMarkedBy:id,full_name',
            'publicShare',
        ]);

        return view($request->user()->isAdmin() ? 'admin.incidents.show' : 'management.incidents.show', [
            'incident' => $incident,
            'sidebarItems' => $request->user()->isAdmin()
                ? null
                : $this->managementSidebar($request->user()->getRoleName()),
        ]);
    }

    public function edit(Request $request, Incident $incident): View
    {
        $this->authorize('update', $incident);
        $incident->load([
            'branch.company',
            'userParticipants.user.role',
            'externalParticipants',
        ]);
        $branches = collect([$incident->branch]);
        $participantOptions = $this->participantOptions->forBranches($branches);
        $knownIds = collect($participantOptions[$incident->branch_id] ?? [])->pluck('id');

        foreach ($incident->userParticipants as $participant) {
            if ($knownIds->contains($participant->user_id)) {
                continue;
            }

            $participantOptions[$incident->branch_id][] = [
                'id' => $participant->user_id,
                'name' => $participant->user->full_name,
                'role' => $participant->user->getRoleName(),
                'role_label' => $participant->user->getRoleName() === 'company_leader'
                    ? 'კომპანიის ხელმძღვანელი'
                    : 'პასუხისმგებელი პირი',
            ];
        }

        return view($request->user()->isAdmin() ? 'admin.incidents.edit' : 'management.incidents.edit', [
            'incident' => $incident,
            'branches' => $branches,
            'participantOptions' => $participantOptions,
            'sidebarItems' => $request->user()->isAdmin()
                ? null
                : $this->managementSidebar($request->user()->getRoleName()),
        ]);
    }

    public function update(UpdateIncidentRequest $request, Incident $incident): RedirectResponse
    {
        try {
            $result = $this->incidentManager->update($incident, $request->validated(), $request->user());

            if ($result['new_user_participants']->isNotEmpty() || $result['new_external_participants']->isNotEmpty()) {
                $this->smsNotifier->notifyCreated(
                    $result['incident'],
                    $result['new_user_participants'],
                    $result['became_public'] ? collect() : $result['new_external_participants']
                );
            }

            if ($result['became_public']) {
                $this->smsNotifier->notifyPublicLinkAvailable($result['incident']);
            }
        } catch (Throwable $e) {
            Log::error('Incident update failed', [
                'incident_id' => $incident->id,
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'incident' => 'ინციდენტის განახლება ვერ მოხერხდა. გთხოვთ, სცადოთ თავიდან.',
            ]);
        }

        $route = $request->user()->isAdmin() ? 'incidents.show' : 'management.incidents.show';

        return redirect()->route($route, $result['incident'])
            ->with('success', 'ინციდენტი წარმატებით განახლდა.');
    }

    public function document(Incident $incident): BinaryFileResponse
    {
        $this->authorize('view', $incident);

        abort_unless(Storage::disk('local')->exists($incident->document_path), 404);

        $response = response()->file(
            Storage::disk('local')->path($incident->document_path),
            [
                'Content-Type' => $incident->document_mime_type ?: 'application/octet-stream',
                'Cache-Control' => 'private, no-store',
            ]
        );

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $incident->document_original_name
        );

        return $response;
    }

    public function downloadDocument(Incident $incident): BinaryFileResponse
    {
        $this->authorize('view', $incident);

        abort_unless(Storage::disk('local')->exists($incident->document_path), 404);

        return response()->download(
            Storage::disk('local')->path($incident->document_path),
            $incident->document_original_name,
            [
                'Content-Type' => $incident->document_mime_type ?: 'application/octet-stream',
                'Cache-Control' => 'private, no-store',
            ]
        );
    }

    public function sign(Request $request, Incident $incident): RedirectResponse
    {
        $this->authorize('sign', $incident);

        $incident->userParticipants()
            ->where('user_id', $request->user()->id)
            ->whereNull('signed_at')
            ->update(['signed_at' => now(), 'updated_at' => now()]);

        return back()->with('success', 'დოკუმენტის ხელმოწერა დადასტურებულია.');
    }

    public function markExternalSigned(
        Request $request,
        Incident $incident,
        IncidentExternalParticipant $externalParticipant
    ): RedirectResponse {
        $this->authorize('manageExternalSignatures', $incident);
        abort_unless((int) $externalParticipant->incident_id === (int) $incident->id, 404);

        if ($externalParticipant->signed_at === null) {
            $externalParticipant->update([
                'signed_at' => now(),
                'signed_marked_by_user_id' => $request->user()->id,
            ]);
        }

        return back()->with('success', 'გარე პირის ხელმოწერა დადასტურებულია.');
    }

    private function managementSidebar(string $role): array
    {
        return config('sidebar.' . str_replace('_', '-', $role), []);
    }
}
