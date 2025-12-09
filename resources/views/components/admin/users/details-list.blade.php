@props(['user', 'companies', 'branches', 'services', 'tasks'])
<ul class="list-group list-group-flush mb-3">
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>როლი:</span>
        <span class="badge bg-primary rounded-pill">
            {{ $user->role->display_name }}
        </span>
    </li>
    @switch($user->getRoleName())
        @case('company_leader')
            <x-ui.info-dropdown-item 
                label="კომპანიები" 
                icon="bi bi-building" 
                name="companies_dropdown" 
                :items="$companies"
                :getItemText="fn($company) => $company->name" 
            />  
            @break

        @case('responsible_person')
            <x-ui.info-dropdown-item 
                label="ფილიალები" 
                icon="bi bi-diagram-3" 
                name="branches_dropdown" 
                :items="$branches"
                :getItemText="fn($branch) => $branch->name" 
            />

            <x-ui.info-dropdown-item 
                label="წვდომა სერვისებზე" 
                icon="bi bi-ui-checks" 
                name="services_dropdown"
                :items="$services" 
                :getItemText="fn($service) => $service->title->ka ?? $service->title->en" 
            />
            @break

        @case('worker')
            <x-ui.info-dropdown-item 
                label="სამუშაოები" 
                icon="bi bi-list-ol" 
                name="tasks_dropdown" 
                :items="$tasks"
                :getItemText="fn($task) => $task->status->display_name . ' / ' . ($task->service?->title ? $task->service->title->ka : $task->service_name_snapshot)"/>
            @break
    @endSwitch

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
