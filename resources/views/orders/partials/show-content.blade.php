@php
    $isAdmin = auth()->user()->isAdmin();
    $previewRoute = $isAdmin ? 'orders.document' : 'management.orders.document';
    $downloadRoute = $isAdmin ? 'orders.document.download' : 'management.orders.document.download';
    $editRoute = $isAdmin ? 'orders.edit' : 'management.orders.edit';
    $ownParticipation = $order->userParticipants->firstWhere('user_id', auth()->id());
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <a href="{{ $isAdmin ? route('orders.index') : route('management.orders.index') }}"
            class="text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>ბრძანებები
        </a>
        <h1 class="h4 mt-2 mb-1">{{ $order->title }}</h1>
        <div class="text-muted">
            {{ $order->branch->company?->name }} · {{ $order->branch->name }}
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2">
        @include('incidents.partials.document-preview-trigger', [
            'incident' => $order,
            'previewRoute' => $previewRoute,
            'downloadRoute' => $downloadRoute,
            'buttonClass' => 'btn btn-outline-primary',
            'label' => 'დოკუმენტის ნახვა',
        ])
        @can('update', $order)
            <a href="{{ route($editRoute, $order) }}" class="btn btn-primary">
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
                <div class="fw-semibold">{{ $order->creator->full_name }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">შექმნის თარიღი</div>
                <div class="fw-semibold">{{ $order->created_at->format('d.m.Y H:i') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">დოკუმენტის წვდომა</div>
                <span class="badge {{ $order->isPublic() ? 'text-bg-info' : 'text-bg-secondary' }}">
                    {{ $order->isPublic() ? 'საჯარო' : 'პირადი' }}
                </span>
            </div>
        </div>
    </div>
</div>

@can('update', $order)
    @if ($order->isPublic() && $order->publicShare?->isUsable())
        <div class="alert alert-info d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
            <div>
                <i class="bi bi-share me-1"></i>
                <strong>საჯარო ბმული:</strong>
                <a href="{{ route('public-shares.show', $order->publicShare->token) }}" target="_blank" rel="noopener">
                    {{ route('public-shares.show', $order->publicShare->token) }}
                </a>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary"
                onclick="navigator.clipboard.writeText(@js(route('public-shares.show', $order->publicShare->token)))">
                ბმულის კოპირება
            </button>
        </div>
    @endif
@endcan

@if ($ownParticipation && !$ownParticipation->signed_at)
    <div class="alert alert-warning d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
        <div>
            <i class="bi bi-pen me-1"></i>
            ამ ბრძანების დოკუმენტი თქვენს ხელმოწერას ელოდება.
        </div>
        <form method="POST" action="{{ route('management.orders.sign', $order) }}">
            @csrf
            <button type="submit" class="btn btn-success">ხელმოწერის დადასტურება</button>
        </form>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent fw-semibold">ხელმომწერები</div>
    <div class="list-group list-group-flush">
        @foreach ($order->userParticipants as $participant)
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
