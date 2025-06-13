<div class="card border-0 shadow-sm rounded-4 h-100">
    <div class="card-header bg-white border-0 rounded-top-4 py-3 px-4 d-flex align-items-start">
        <div class="d-flex align-items-center">
            <div class="bg-light p-2 rounded-circle me-3 d-flex align-items-center justify-content-center">
                <i class="bi {{ $icon }} text-secondary fs-5"></i>
            </div>
            <div>
                <h5 class="mb-1 fw-semibold text-dark">{{ $title }}</h5>
                <small class="text-muted">რაოდენობა: {{ $count }}</small>
            </div>
        </div>
    </div>

    <div class="card-body px-4 pt-2 pb-4">
        <div class="d-flex flex-wrap justify-content-end gap-2">
            <a href="{{ $viewRoute }}"
                class="btn btn-sm btn-outline-primary rounded-pill px-3 d-inline-flex align-items-center shadow-sm">
                <span class="small">ნახვა</span>
                <i class="bi bi-eye ms-2 fs-6"></i>
            </a>
            <a href="{{ $createRoute }}"
                class="btn btn-sm btn-success rounded-pill px-3 d-inline-flex align-items-center shadow-sm">
                <span class="small">შექმნა</span>
                <i class="bi bi-plus-lg ms-2 fs-6"></i>
            </a>
        </div>
    </div>
</div>