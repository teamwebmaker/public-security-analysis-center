@props(['program'])

<ul class="list-group list-group-flush mb-3">
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>ფასი:</span>
        <span class="badge bg-primary rounded-pill" style="font-size: 13px;">
            {{ $program->price }} ₾
        </span>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>ხანგრძლივობა:</span>
        <span>{{ $program->duration }}</span>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>საათები:</span>
        <span>{{ $program->hour->start }} - {{ $program->hour->end }}</span>
    </li>
    <li class="list-group-item">
        <div class="d-flex justify-content-between">
            <span>დღეები:</span>
            <select>
                <option class="badge bg-secondary me-1">
                    <span>სულ</span> {{ count($program->days->ka ?? []) }}
                </option>
                @foreach($program->days->ka ?? [] as $day)
                    <option class="badge bg-secondary me-1" disabled>{{ $day }}</option>
                @endforeach
            </select>
        </div>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>სტატუსი:</span>
        <span class="badge {{ $program->visibility ? 'bg-success' : 'bg-warning' }}">
            {{ $program->visibility ? 'ხილული' : 'დამალული' }}
        </span>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>დაწყება:</span>
        <span>{{ $program->start_date }}</span>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>დასრულება:</span>
        <span>{{ $program->end_date }}</span>
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
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