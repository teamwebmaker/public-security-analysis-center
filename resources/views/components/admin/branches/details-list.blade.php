@props(['branch'])
<ul class="list-group list-group-flush mb-3">
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>მშობელი კომპანია:</span>
        <span class="badge bg-primary rounded-pill">
            {{ $branch->company->name }}
        </span>
    </li>
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>მისამართი:</span>
        <label class="text-truncate d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top"
            data-bs-custom-class="custom-tooltip" data-bs-title="{{ $branch->address }}"
            style="max-width: 150px; cursor: pointer;">
            <span>{{ $branch->address }}</span>
        </label>
    </li>
</ul>