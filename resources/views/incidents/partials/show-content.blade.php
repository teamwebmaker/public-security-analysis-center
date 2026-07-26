@php
    $isAdmin = auth()->user()->isAdmin();
    $documentRoute = $isAdmin ? 'incidents.document' : 'management.incidents.document';
    $documentDownloadRoute = $isAdmin
        ? 'incidents.document.download'
        : 'management.incidents.document.download';
    $editRoute = $isAdmin ? 'incidents.edit' : 'management.incidents.edit';
    $externalSignRoute = $isAdmin
        ? 'incidents.external-participants.sign'
        : 'management.incidents.external-participants.sign';
    $ownParticipation = $incident->userParticipants->firstWhere('user_id', auth()->id());
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <a href="{{ $isAdmin ? route('incidents.index') : route('management.incidents.index') }}"
            class="text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>ინციდენტები
        </a>
        <h1 class="h4 mt-2 mb-1">{{ $incident->title }}</h1>
        <div class="text-muted">
            {{ $incident->branch->company?->name }} · {{ $incident->branch->name }}
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2">
        @include('incidents.partials.document-preview-trigger', [
            'previewRoute' => $documentRoute,
            'downloadRoute' => $documentDownloadRoute,
            'buttonClass' => 'btn btn-outline-primary',
            'label' => 'დოკუმენტის ნახვა',
        ])
        @can('update', $incident)
            <a href="{{ route($editRoute, $incident) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i>
                რედაქტირება
            </a>
        @endcan
    </div>
</div>

@include('incidents.partials.document-preview-modal')

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">შემქმნელი</div>
                <div class="fw-semibold">{{ $incident->creator->full_name }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">შექმნის თარიღი</div>
                <div class="fw-semibold">{{ $incident->created_at->format('d.m.Y H:i') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">დოკუმენტის წვდომა</div>
                @if ($incident->isPublic())
                    <span class="badge text-bg-info">საჯარო</span>
                @else
                    <span class="badge text-bg-secondary">პირადი</span>
                @endif
            </div>
        </div>
    </div>
</div>

@can('update', $incident)
    @if ($incident->isPublic() && $incident->publicShare?->isUsable())
        <div class="alert alert-info d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
            <div>
                <i class="bi bi-share me-1"></i>
                <strong>საჯარო ბმული:</strong>
                <a href="{{ route('public-shares.show', $incident->publicShare->token) }}" target="_blank" rel="noopener">
                    {{ route('public-shares.show', $incident->publicShare->token) }}
                </a>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary"
                onclick="navigator.clipboard.writeText(@js(route('public-shares.show', $incident->publicShare->token)))">
                ბმულის კოპირება
            </button>
        </div>
    @endif
@endcan

@if ($ownParticipation && !$ownParticipation->signed_at)
    <div class="alert alert-warning d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
        <div>
            <i class="bi bi-pen me-1"></i>
            ამ ინციდენტის დოკუმენტი თქვენს ხელმოწერას ელოდება.
        </div>
        <form method="POST" action="{{ route('management.incidents.sign', $incident) }}">
            @csrf
            <button type="submit" class="btn btn-success">
                ხელმოწერის დადასტურება
            </button>
        </form>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent fw-semibold">სისტემის მომხმარებლები</div>
            <div class="list-group list-group-flush">
                @foreach ($incident->userParticipants as $participant)
                    <div class="list-group-item d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="fw-semibold">{{ $participant->user->full_name }}</div>
                            <small class="text-muted">
                                {{ $participant->user->getRoleName() === 'company_leader'
                                    ? 'კომპანიის ხელმძღვანელი'
                                    : 'პასუხისმგებელი პირი' }}
                            </small>
                        </div>
                        @if ($participant->signed_at)
                            <span class="badge text-bg-success">
                                ხელმოწერილია · {{ $participant->signed_at->format('d.m.Y H:i') }}
                            </span>
                        @else
                            <span class="badge text-bg-warning">ელოდება</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent fw-semibold">სხვა მონაწილე პირები</div>
            <div class="list-group list-group-flush">
                @forelse ($incident->externalParticipants as $participant)
                    <div class="list-group-item d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="fw-semibold">{{ $participant->full_name }}</div>
                            @if ($participant->phone)
                                <small class="text-muted">{{ $participant->phone }}</small>
                            @endif
                            @if ($participant->signed_at && $participant->signedMarkedBy)
                                <div class="small text-muted">
                                    მონიშნა: {{ $participant->signedMarkedBy->full_name }}
                                </div>
                            @endif
                        </div>
                        <div class="text-end">
                            @if ($participant->signed_at)
                                <span class="badge text-bg-success">
                                    ხელმოწერილია · {{ $participant->signed_at->format('d.m.Y H:i') }}
                                </span>
                            @else
                                @can('manageExternalSignatures', $incident)
                                    <form method="POST"
                                        action="{{ route($externalSignRoute, [$incident, $participant]) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            ხელმოწერილად მონიშვნა
                                        </button>
                                    </form>
                                @else
                                    <span class="badge text-bg-warning">ელოდება</span>
                                @endcan
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="list-group-item text-muted py-4 text-center">სხვა პირები არ არის დამატებული</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
