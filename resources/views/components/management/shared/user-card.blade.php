@props([
    'name',
    'email' => null,
    'number',
    'role' => null,
    'roleColor' => 'primary',
    'status' => 'online', // or 'offline'
    'additionalData'
])
<div class="card border-0 shadow-sm rounded-3 p-3 m-2">
    <div class="d-flex align-items-center justify-content-between flex-wrap">
        <div class="d-flex align-items-center gap-3">
            <div class="position-relative">
                <div class="bg-light d-flex justify-content-center align-items-center rounded-circle"
                    style="width: 48px; height: 48px;">
                    <i class="bi bi-person fs-4 text-secondary"></i>
                </div>
                {{-- @if($status === 'online')
                <span
                    class="position-absolute bottom-0 end-0 translate-middle p-1 bg-success border border-white rounded-circle"></span>
                @endif --}}
            </div>
            <div>
                <h6 class="mb-1 fw-semibold">{{ $name }}</h6>
                <div class="d-flex flex-wrap gap-2 small text-muted">
                    @if($email)
                        <span><i class="bi bi-envelope me-1"></i>{{ $email }}</span>
                    @endif
                    {{-- <span><i class="bi bi-calendar me-1"></i>{{ $joined }}</span> --}}
                    <span><i class="bi bi-telephone me-1"></i></i>{{$number}}</span>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3 mt-3 mt-sm-0">
            @if ($role && $roleColor)
                <span class="badge bg-{{ $roleColor }} bg-opacity-25 text-{{ $roleColor }} d-flex align-items-center">
                    <i class="bi bi-person-badge-fill me-1"></i>{{ $role }}
                </span>
            @endif

            <div class="text-end small">
                {{ $additionalData }}
                {{-- <div class="fw-semibold text-dark">{{ $workflowsCount }} workflows</div>
                <div class="text-muted">created</div> --}}
            </div>
        </div>
    </div>
</div>