@props(['program'])

<ul class="list-group list-group-flush mb-3">
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>სტატუსი:</span>
        <span class="badge {{ $program->visibility ? 'bg-success' : 'bg-warning' }}">
            {{ $program->visibility ? 'ხილული' : 'დამალული' }}
        </span>
    </li>
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>ფასი:</span>
        <span class="badge bg-primary rounded-pill" style="font-size: 13px;">
            {{ $program->price }} ₾
        </span>
    </li>

    <x-ui.info-dropdown-item label="დღეები" icon="bi bi-calendar2-week-fill" name="days_dropdown"
        :items="collect($program->days->ka ?? [])" :getItemText="fn($day) => $day" />

    <x-ui.info-dropdown-item label="მენტორები" icon="bi bi-person-lines-fill" name="mentors_dropdown"
        :items="$program->mentors" :getItemText="fn($mentor) => $mentor->full_name" />

    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>ხანგრძლივობა:</span>
        <span>{{ $program->duration }}</span>
    </li>
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>საათები:</span>
        <span>{{ $program->hour->start }} - {{ $program->hour->end }}</span>
    </li>
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>დაწყება:</span>
        <span>{{ $program->start_date }}</span>
    </li>
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>დასრულება:</span>
        <span>{{ $program->end_date }}</span>
    </li>

    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>ლოკაცია:</span>
        <label class="text-truncate d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top"
            data-bs-custom-class="custom-tooltip" data-bs-title="{{ $program->address }}"
            style="max-width: 150px; cursor: pointer;">
            <span>{{ $program->address }}</span>
        </label>
    </li>

    @if ($program->video)
        <li class="list-group-item">
            <a href="{{ $program->video }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                <i class="bi bi-play-circle me-2"></i>ვიდეოს ნახვა
            </a>
        </li>
    @endif

    @if ($program->certificate_image)
        <li class="list-group-item">
            <a href="{{ asset('images/certificates/programs/' . $program->certificate_image) }}" data-fancybox
                class="btn btn-sm btn-outline-success w-100">
                <i class="bi bi-award me-2"></i>სერტიფიკატის ნახვა
            </a>
        </li>
    @endif
</ul>