@php
    $isAdmin = auth()->user()->isAdmin();
    $showRoute = $isAdmin ? 'orders.show' : 'management.orders.show';
    $previewRoute = $isAdmin ? 'orders.document' : 'management.orders.document';
    $downloadRoute = $isAdmin ? 'orders.document.download' : 'management.orders.document.download';
    $createRoute = $isAdmin ? 'orders.create' : 'management.orders.create';
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h1 class="h4 mb-1">ბრძანებები</h1>
        <p class="text-muted mb-0">თქვენთან დაკავშირებული ბრძანებები და ხელმოწერის სტატუსები</p>
    </div>

    <a href="{{ route($createRoute) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>
        ბრძანების შექმნა
    </a>
</div>

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
                    @forelse ($orders as $order)
                        @php
                            $allParticipants = $order->userParticipants->count();
                            $signedParticipants = $order->userParticipants->whereNotNull('signed_at')->count();
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route($showRoute, $order) }}" class="fw-semibold text-decoration-none">
                                    {{ $order->title }}
                                </a>
                            </td>
                            <td>
                                <div>{{ $order->branch->name }}</div>
                                <small class="text-muted">{{ $order->branch->company?->name }}</small>
                            </td>
                            <td>
                                @include('incidents.partials.document-preview-trigger', [
                                    'incident' => $order,
                                    'previewRoute' => $previewRoute,
                                    'downloadRoute' => $downloadRoute,
                                ])
                            </td>
                            <td>
                                <span class="badge {{ $signedParticipants === $allParticipants ? 'text-bg-success' : 'text-bg-warning' }}">
                                    {{ $signedParticipants }} / {{ $allParticipants }}
                                </span>
                            </td>
                            <td>
                                @if ($order->isPublic())
                                    <span class="badge text-bg-info">საჯარო</span>
                                @else
                                    <span class="badge text-bg-secondary">პირადი</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route($showRoute, $order) }}" class="btn btn-sm btn-outline-primary">
                                    დეტალები
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                ბრძანებები ვერ მოიძებნა
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if ($orders->hasPages())
    <div class="mt-3">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>
@endif

@include('incidents.partials.document-preview-modal')
