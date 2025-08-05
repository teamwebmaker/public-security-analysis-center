@props(['branch'])
<div class="d-flex align-items-center justify-content-between">
    <div class="d-flex">
        <i class="bi bi-geo-alt-fill text-primary fs-5 mt-1 me-2"></i>
        <div>
            <h6 class="mb-1 fw-semibold">{{ $branch->name }}</h6>
            <p class="small text-muted mb-0">{{ $branch->address }}</p>
        </div>
    </div>
    <div>
        <!-- responsible users modal trigger -->
        <a :href="route('management.branch.responsible-users', $branch->id)" data-bs-toggle="modal"
            data-bs-target="#responsible-users-modal">
            <i class=" bi bi-people-fill fs-4 text-primary"></i>
        </a>
    </div>
</div>