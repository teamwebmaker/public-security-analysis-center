@props(['user'])
<ul class="list-group list-group-flush mb-3">
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>როლი:</span>
        <span class="badge bg-primary rounded-pill">
            {{ $user->role->display_name }}
            @php
             @endphp
        </span>
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