@if (($dashboardIncidents ?? collect())->isNotEmpty())
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <span class="fw-semibold">
                <i class="bi bi-exclamation-triangle me-1"></i>
                ინციდენტები
            </span>
            <a href="{{ route('management.incidents.index') }}" class="btn btn-sm btn-outline-primary">ყველას ნახვა</a>
        </div>
        <div class="list-group list-group-flush">
            @foreach ($dashboardIncidents as $dashboardIncident)
                <a href="{{ route('management.incidents.show', $dashboardIncident) }}"
                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-3">
                    <div>
                        <div class="fw-semibold">{{ $dashboardIncident->title }}</div>
                        <small class="text-muted">
                            {{ $dashboardIncident->branch->company?->name }} · {{ $dashboardIncident->branch->name }}
                        </small>
                    </div>
                    @php
                        $mySignature = $dashboardIncident->userParticipants
                            ->firstWhere('user_id', auth()->id());
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
