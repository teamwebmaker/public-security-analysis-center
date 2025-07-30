@props(['branch', 'users'])
<ul class="list-group list-group-flush mb-3">

    <li class="list-group-item">
        <div class="d-flex justify-content-between flex-wrap align-items-center">
            <span>პასუხის მგებელი პირები:</span>
            @if ($users->isNotEmpty())
                <div class="position-relative d-inline-block" style="width: 50px; height: 25px;">
                    <!-- The icon -->
                    <div class=" d-flex justify-content-center align-items-center gap-1">
                        <i class="bi bi-person-lines-fill fs-4 text-primary-emphasis"></i>
                        <i class=" bi bi-caret-down-fill text-primary-emphasis"></i>
                    </div>
                    <!-- Invisible select over the icon -->
                    <select name="mentors_dropdown" class="position-absolute top-0 start-0 opacity-0"
                        style="width: 100%; height: 100%;">
                        @foreach ($users as $user)
                            <option> {{ $user->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <span class="badge bg-secondary rounded-pill">არ არის მითითებული</span>
            @endif

        </div>
    </li>
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