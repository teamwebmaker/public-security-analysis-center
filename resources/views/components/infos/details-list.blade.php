@props(['info'])
<ul class="list-group list-group-flush mb-3">
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>გამოცდილება:</span>
        <span class="badge bg-primary rounded-pill">
            {{ $info->experience }}
        </span>
    </li>
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>კურსდამთავრებული:</span>
        <span class="badge bg-primary rounded-pill">
            {{ $info->graduates }}
        </span>
    </li>
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>ელ.ფოსტა:</span>
        <span class="">
            {{ $info->email }}
        </span>
    </li>
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>ტელეფონის ნომერი:</span>
        <span>
            {{ $info->phone }}
        </span>
    </li>
</ul>