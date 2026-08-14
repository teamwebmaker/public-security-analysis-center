@props(['instruction', 'resourceName'])

<ul class="list-group list-group-flush mb-3">
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>სტატუსი:</span>
        <span class="badge {{ $instruction->visibility ? 'bg-success' : 'bg-warning' }}">
            {{ $instruction->visibility ? 'ხილული' : 'დამალული' }}
        </span>
    </li>
    <x-ui.info-dropdown-item label="ყურება ნებადართული შემსრულებლები" icon="bi bi-person-lines-fill"
        name="mentors_dropdown" :items="$instruction->users" :getItemText="fn($worker) => $worker->full_name" />
    @if ($instruction->link)
        <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
            <span>ლინკი:</span>
            <label class="text-truncate d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-custom-class="custom-tooltip" data-bs-title="{{ $instruction->link }}"
                style="max-width: 150px; cursor: pointer;">
                <a href="{{ $instruction->link }}" target="_blank" class="text-decoration-none text-reset">
                    <span>{{ $instruction->link }}</span>
                </a>
            </label>
        </li>
    @endif

    <li class="list-group-item d-flex justify-content-between bg-transparent flex-wrap align-items-center">
        @include('instructions.partials.document-actions', [
            'previewRoute' => 'instructions.document',
            'downloadRoute' => 'instructions.document.download',
        ])
    </li>
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>გაზიარება:</span>
        <span class="badge {{ $instruction->isPublic() ? 'text-bg-info' : 'text-bg-secondary' }}">
            {{ $instruction->isPublic() ? 'საჯარო' : 'პირადი' }}
        </span>
    </li>
    @if ($instruction->isPublic() && $instruction->publicShare?->isUsable())
        <li class="list-group-item">
            <div class="small text-muted mb-1">საჯარო ბმული</div>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control"
                    value="{{ route('public-shares.show', $instruction->publicShare->token) }}" readonly>
                <button type="button" class="btn btn-outline-primary"
                    onclick="navigator.clipboard.writeText(@js(route('public-shares.show', $instruction->publicShare->token)))">
                    კოპირება
                </button>
            </div>
        </li>
    @endif
</ul>
