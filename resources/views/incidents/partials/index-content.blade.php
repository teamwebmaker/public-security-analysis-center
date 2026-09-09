@php
    $isAdmin = auth()->user()->isAdmin();
    $isWorker = auth()->user()->getRoleName() === 'worker';
    $indexRoute = $isAdmin ? 'incidents.index' : 'management.incidents.index';
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h1 class="h4 mb-1">ინციდენტები</h1>
        <p class="text-muted mb-0">თქვენთან დაკავშირებული ინციდენტები და ხელმოწერის სტატუსები</p>
    </div>

    @if ($isWorker)
        <a href="{{ route('management.incidents.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            ინციდენტის შექმნა
        </a>
    @endif
</div>

<x-shared.resource-list-filters
    :action="route($indexRoute)"
    searchPlaceholder="დასახელება, დოკუმენტი, კომპანია, ფილიალი ან მონაწილე"
    :filters="[
        'company_id' => [
            'label' => 'კომპანია',
            'placeholder' => 'ყველა კომპანია',
            'options' => $filterOptions['companies'],
        ],
        'branch_id' => [
            'label' => 'ფილიალი',
            'placeholder' => 'ყველა ფილიალი',
            'options' => $filterOptions['branches'],
        ],
        'document_visibility' => [
            'label' => 'გაზიარება',
            'options' => ['public' => 'საჯარო', 'private' => 'პირადი'],
        ],
        'signature_status' => [
            'label' => 'ხელმოწერები',
            'options' => ['complete' => 'ყველა ხელმოწერილია', 'pending' => 'ხელმოწერა დარჩენილია'],
        ],
    ]" />

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">დასახელება</th>
                        <th>ფილიალი</th>
                        <th>დოკუმენტი</th>
                        <th>ხელმოწერები</th>
                        <th>წვდომა</th>
                        <th class="text-end pe-3">მოქმედება</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($incidents as $incident)
                        @php
                            $allParticipants = $incident->userParticipants->count() + $incident->externalParticipants->count();
                            $signedParticipants = $incident->userParticipants->whereNotNull('signed_at')->count()
                                + $incident->externalParticipants->whereNotNull('signed_at')->count();
                            $showRoute = $isAdmin ? 'incidents.show' : 'management.incidents.show';
                            $documentRoute = $isAdmin ? 'incidents.document' : 'management.incidents.document';
                            $documentDownloadRoute = $isAdmin
                                ? 'incidents.document.download'
                                : 'management.incidents.document.download';
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route($showRoute, $incident) }}" class="fw-semibold text-decoration-none">
                                    {{ $incident->title }}
                                </a>
                            </td>
                            <td>
                                <div>{{ $incident->branch->name }}</div>
                                <small class="text-muted">{{ $incident->branch->company?->name }}</small>
                            </td>
                            <td>
                                @include('incidents.partials.document-preview-trigger', [
                                    'previewRoute' => $documentRoute,
                                    'downloadRoute' => $documentDownloadRoute,
                                ])
                            </td>
                            <td>
                                <span class="badge {{ $signedParticipants === $allParticipants ? 'text-bg-success' : 'text-bg-warning' }}">
                                    {{ $signedParticipants }} / {{ $allParticipants }}
                                </span>
                            </td>
                            <td>
                                @if ($incident->isPublic())
                                    <span class="badge text-bg-info">საჯარო</span>
                                @else
                                    <span class="badge text-bg-secondary">პირადი</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route($showRoute, $incident) }}" class="btn btn-sm btn-outline-primary">
                                    დეტალები
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                ინციდენტები ვერ მოიძებნა
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if ($incidents->hasPages())
    <div class="mt-3">
        {{ $incidents->links('pagination::bootstrap-5') }}
    </div>
@endif

@include('incidents.partials.document-preview-modal')
