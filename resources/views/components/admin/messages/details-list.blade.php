@props(['message'])
@php
    $badgeClasses = [
        'Register to program' => 'badge bg-primary',
        'Register to Service' => 'badge bg-info',
    ];

    $class = $badgeClasses[$message->subject] ?? '';
@endphp
<ul class="list-group list-group-flush mb-3">
    @if ($message->subject)
        <li class="list-group-item d-flex justify-content-between bg-transparent flex-wrap align-items-center">
            <span>დანიშნულება:</span>
            <label class="text-truncate d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-custom-class="custom-tooltip" data-bs-title="{{ $message->subject }}"
                style="max-width: 180px; cursor: pointer;">
                <span class=" {{ $class }}">
                    {{ $message->subject }}
                </span>
            </label>
        </li>

    @endif
    </li>
    @if ($message->phone)
        <li class=" list-group-item d-flex justify-content-between bg-transparent flex-wrap align-items-center">
            <span>ტელეფონი:</span>
            <span class="badge bg-secondary rounded-pill">
                {{ $message->phone }}
            </span>
        </li>
    @endif
    <li class="list-group-item d-flex justify-content-between bg-transparent flex-wrap align-items-center">
        <span>ელ.ფოსტა:</span>
        <span class="badge bg-secondary rounded-pill">
            {{ $message->email }}
        </span>
    </li>
</ul>
