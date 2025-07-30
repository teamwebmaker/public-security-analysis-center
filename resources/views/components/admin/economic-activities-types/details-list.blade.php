@props(['companies'])

<ul class="list-group list-group-flush mb-3">
    @if ($companies->isNotEmpty())
        <li class="list-group-item">
            <div class="d-flex justify-content-between flex-wrap align-items-center">
                <span>კომპანიები:</span>
                <div class="position-relative d-inline-block" style="width: 50px; height: 25px;">
                    <div class="d-flex justify-content-center align-items-center gap-1">
                        <i class="bi bi-building fs-4 text-primary-emphasis"></i>
                        <i class="bi bi-caret-down-fill text-primary-emphasis"></i>
                    </div>
                    <select name="mentors_dropdown" class="position-absolute top-0 start-0 opacity-0"
                        style="width: 100%; height: 100%;">
                        @foreach ($companies as $company)
                            <option>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </li>
    @endif
</ul>