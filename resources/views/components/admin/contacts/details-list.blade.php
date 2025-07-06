@props(['contact'])
<ul class="list-group list-group-flush mb-3">
    @if ($contact->subject)
        <li class="list-group-item d-flex justify-content-between bg-transparent flex-wrap align-items-center">
            <span>დანიშნულება:</span>
            <label class="text-truncate d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-custom-class="custom-tooltip" data-bs-title="{{ $contact->subject }}"
                style="max-width: 150px; cursor: pointer;">
                <span>{{ $contact->subject }}</span>
            </label>
        </li>

    @endif
    </li>
    @if ($contact->phone)
        <li class="list-group-item d-flex justify-content-between bg-transparent flex-wrap align-items-center">
            <span>ტელეფონი:</span>
            <span class="badge bg-primary rounded-pill">
                {{ $contact->phone }}
            </span>
        </li>
    @endif
    <li class="list-group-item d-flex justify-content-between bg-transparent flex-wrap align-items-center">
        <span>ელ.ფოსტა:</span>
        <span class="badge bg-primary rounded-pill">
            {{ $contact->email }}
        </span>
    </li>
</ul>