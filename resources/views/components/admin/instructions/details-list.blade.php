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
        <x-ui.document-link :file="$instruction->document" :path="'documents/' . $resourceName" label="დოკუმენტი" />
    </li>
</ul>