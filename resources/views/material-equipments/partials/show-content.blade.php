@php
    $isAdmin = auth()->user()->isAdmin();
    $routePrefix = $isAdmin ? 'material-equipments' : 'management.material-equipments';
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <a href="{{ route("{$routePrefix}.index") }}" class="text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>მატერიალურ-ტექნიკური აღჭ
        </a>
        <h1 class="h4 mt-2 mb-1">{{ $materialEquipment->title }}</h1>
        <div class="text-muted">
            {{ $materialEquipment->branch->company?->name }} · {{ $materialEquipment->branch->name }}
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2">
        @include('incidents.partials.document-preview-trigger', [
            'incident' => $materialEquipment,
            'previewRoute' => "{$routePrefix}.document",
            'downloadRoute' => "{$routePrefix}.document.download",
            'buttonClass' => 'btn btn-outline-primary',
            'label' => 'დოკუმენტის ნახვა',
        ])
        @can('update', $materialEquipment)
            <a href="{{ route("{$routePrefix}.edit", $materialEquipment) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i>რედაქტირება
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
                <div class="fw-semibold">{{ $materialEquipment->creator->full_name }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">შექმნის თარიღი</div>
                <div class="fw-semibold">{{ $materialEquipment->created_at->format('d.m.Y H:i') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">დოკუმენტის წვდომა</div>
                <span class="badge {{ $materialEquipment->isPublic() ? 'text-bg-info' : 'text-bg-secondary' }}">
                    {{ $materialEquipment->isPublic() ? 'საჯარო' : 'პირადი' }}
                </span>
            </div>
        </div>
    </div>
</div>

@can('update', $materialEquipment)
    @if ($materialEquipment->isPublic() && $materialEquipment->publicShare?->isUsable())
        <div class="alert alert-info d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
            <div>
                <i class="bi bi-share me-1"></i>
                <strong>საჯარო ბმული:</strong>
                <a href="{{ route('public-shares.show', $materialEquipment->publicShare->token) }}"
                    target="_blank" rel="noopener">
                    {{ route('public-shares.show', $materialEquipment->publicShare->token) }}
                </a>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary"
                onclick="navigator.clipboard.writeText(@js(route('public-shares.show', $materialEquipment->publicShare->token)))">
                ბმულის კოპირება
            </button>
        </div>
    @endif
@endcan

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent fw-semibold">უკვე ხელმომწერი პირები</div>
    <div class="list-group list-group-flush">
        @foreach ($materialEquipment->signatures as $signature)
            <div class="list-group-item d-flex justify-content-between align-items-start gap-3">
                <div>
                    <div class="fw-semibold">{{ $signature->user->full_name }}</div>
                    <small class="text-muted">
                        {{ $signature->user->getRoleName() === 'company_leader'
                            ? 'კომპანიის ხელმძღვანელი'
                            : 'პასუხისმგებელი პირი' }}
                    </small>
                </div>
                <span class="badge text-bg-success">
                    ხელმოწერილია · {{ $signature->signed_at->format('d.m.Y H:i') }}
                </span>
            </div>
        @endforeach
    </div>
</div>
