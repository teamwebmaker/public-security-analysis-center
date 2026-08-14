@props(['instruction', 'resourceName'])

<ul class="list-group list-group-flush mb-3">
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
    <li class="list-group-item d-flex justify-content-between mt-3 bg-transparent flex-wrap align-items-center">
        @include('instructions.partials.document-actions', [
            'previewRoute' => 'management.worker.instructions.document',
            'downloadRoute' => 'management.worker.instructions.document.download',
        ])
    </li>
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>გაზიარება:</span>
        <span class="badge {{ $instruction->isPublic() ? 'text-bg-info' : 'text-bg-secondary' }}">
            {{ $instruction->isPublic() ? 'საჯარო' : 'პირადი' }}
        </span>
    </li>
</ul>
