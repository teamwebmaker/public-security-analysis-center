@props(['user', 'companies', 'branches'])
<ul class="list-group list-group-flush mb-3">
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>როლი:</span>
        <span class="badge bg-primary rounded-pill">
            {{ $user->role->display_name }}
        </span>
    </li>

    <li class="list-group-item">
        <div class="d-flex justify-content-between flex-wrap align-items-center">
            <span>კომპანიები:</span>
            @if ($companies->isNotEmpty())

                <div class="position-relative d-inline-block" style="width: 50px; height: 25px;">
                    <!-- The icon -->
                    <div class=" d-flex justify-content-center align-items-center gap-1">
                        <i class="bi bi-building fs-4 text-primary-emphasis"></i>
                        <i class=" bi bi-caret-down-fill text-primary-emphasis"></i>
                    </div>
                    <!-- Invisible select over the icon -->
                    <select name="mentors_dropdown" class="position-absolute top-0 start-0 opacity-0"
                        style="width: 100%; height: 100%;">
                        @foreach ($companies as $company)
                            <option> {{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <span class="badge bg-secondary rounded-pill"><i class="bi bi-x-lg"></i></span>
            @endif
        </div>
    </li>
    <li class="list-group-item">
        <div class="d-flex justify-content-between flex-wrap align-items-center">
            <span>ფილიალები:</span>
            @if ($branches->isNotEmpty())

                <div class="position-relative d-inline-block" style="width: 50px; height: 25px;">
                    <!-- The icon -->
                    <div class=" d-flex justify-content-center align-items-center gap-1">
                        <i class="bi bi-diagram-3 fs-4 text-primary-emphasis"></i>
                        <i class=" bi bi-caret-down-fill text-primary-emphasis"></i>
                    </div>
                    <!-- Invisible select over the icon -->
                    <select name="mentors_dropdown" class="position-absolute top-0 start-0 opacity-0"
                        style="width: 100%; height: 100%;">
                        @foreach ($branches as $branch)
                            <option> {{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <span class="badge bg-secondary rounded-pill"><i class="bi bi-x-lg"></i></span>
            @endif
        </div>
    </li>
    @if ($user->phone)
        <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
            <span>ნომერი:</span>
            <span>
                {{ $user->phone }}
            </span>
        </li>
    @endif

    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>ელ.ფოსტა:</span>
        <span>
            {{ $user->email ?? 'არ არის მითითებული' }}
        </span>
    </li>
</ul>