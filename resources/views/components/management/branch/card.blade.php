@props(['branch'])
<div class="col-12 col-md-6">
    <div class="card border-0 shadow-sm h-100 transition">
        <div class="card-body p-3 p-md-4">

            <x-management.branch.header :branch="$branch" />

            <hr class="mt-2 mb-3 opacity-25">
            <h6 class="fw-semibold text-secondary mb-3 d-flex align-items-center">
                სამუშაოები:
            </h6>

            <x-management.branch.task-stats :branch="$branch" />

            <x-management.branch.view-more />
        </div>
    </div>
</div>