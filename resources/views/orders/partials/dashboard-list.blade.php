@if (($dashboardOrders ?? collect())->isNotEmpty())
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <span class="fw-semibold">
                <i class="bi bi-file-earmark-check me-1"></i>
                ბრძანებები
            </span>
            <a href="{{ route('management.orders.index') }}" class="btn btn-sm btn-outline-primary">ყველას ნახვა</a>
        </div>
        <div class="list-group list-group-flush">
            @foreach ($dashboardOrders as $dashboardOrder)
                <a href="{{ route('management.orders.show', $dashboardOrder) }}"
                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-3">
                    <div>
                        <div class="fw-semibold">{{ $dashboardOrder->title }}</div>
                        <small class="text-muted">
                            {{ $dashboardOrder->branch->company?->name }} · {{ $dashboardOrder->branch->name }}
                        </small>
                    </div>
                    @php
                        $mySignature = $dashboardOrder->userParticipants->firstWhere('user_id', auth()->id());
                    @endphp
                    @if ($mySignature && !$mySignature->signed_at)
                        <span class="badge text-bg-warning">ხელმოსაწერია</span>
                    @else
                        <i class="bi bi-chevron-right text-muted"></i>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
@endif
