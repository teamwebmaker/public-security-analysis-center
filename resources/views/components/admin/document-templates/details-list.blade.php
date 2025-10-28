@props(['data', 'resourceName'])

<ul class="list-group list-group-flush mb-3">
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>სტატუსი:</span>
        <span class="badge {{ $data->visibility ? 'bg-success' : 'bg-warning' }}">
            {{ $data->visibility ? 'ხილული' : 'დამალული' }}
        </span>
    </li>
    <x-ui.info-dropdown-item label="წვდომა აქვს" icon="bi bi-person-lines-fill" name="mentors_dropdown"
        :items="$data->users" :getItemText="fn($worker) => $worker->full_name" />

    <li class="list-group-item d-flex justify-content-between bg-transparent flex-wrap align-items-center">
        <x-ui.document-link :file="$data->document" :path="'documents/' . $resourceName" label="დოკუმენტი" />
    </li>
</ul>