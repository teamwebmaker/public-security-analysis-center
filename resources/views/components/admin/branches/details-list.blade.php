@props(['branch', 'users'])
<ul class="list-group list-group-flush mb-3">
    <x-ui.info-dropdown-item label="პასუხისმგებელი პირები" icon="bi bi-person-lines-fill"
        name="responsible_users_dropdown" :items="$users" :getItemText="fn($users) => $users->full_name" />
    <li class="list-group-item d-flex justify-content-between bg-transparent flex-wrap align-items-center">
        <span>მშობელი კომპანია:</span>
        <span class="badge bg-primary rounded-pill">
            {{ $branch->company->name }}
        </span>
    </li>
    <li class="list-group-item d-flex justify-content-between bg-transparent flex-wrap align-items-center">
        <span>მისამართი:</span>
        <label class="text-truncate d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top"
            data-bs-custom-class="custom-tooltip" data-bs-title="{{ $branch->address }}"
            style="max-width: 150px; cursor: pointer;">
            <span>{{ $branch->address }}</span>
        </label>
    </li>
</ul>