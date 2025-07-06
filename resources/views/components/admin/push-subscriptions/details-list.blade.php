@props(['subscription'])
<ul class="list-group list-group-flush mb-3">
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>სტატუსი:</span>
        <span class="badge {{ $subscription->approved ? 'bg-success' : 'bg-danger' }}">
            {{ $subscription->approved ? 'დამტკიცდა' : 'დასამტკიცებელი ' }}
        </span>
    </li>

    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>ID:</span>
        <span class="badge bg-secondary">
            {{ $subscription->id }}
        </span>
    </li>
    @if ($subscription->os)
        <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
            <span>OS სისტემა:</span>
            <span class="badge bg-secondary">
                {{ $subscription->os }}
            </span>
        </li>
    @endif
    @if ($subscription->ip_address)
        <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
            <span>IP მისამართი:</span>
            <span class="badge bg-secondary">
                {{ $subscription->ip_address }}
            </span>
        </li>
    @endif
    @if ($subscription->city)
        <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
            <span>ქალაქი:</span>
            <span class="badge bg-secondary">
                {{ $subscription->city }}
            </span>
        </li>
    @endif
    @if ($subscription->user_agent)
        <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
            <span>User agent:</span>
            <label class="text-truncate d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-custom-class="custom-tooltip" data-bs-title="{{ $subscription->user_agent }}"
                style="max-width: 150px; cursor: pointer;">
                <span>{{ $subscription->user_agent }}</span>
            </label>
        </li>
    @endif
</ul>