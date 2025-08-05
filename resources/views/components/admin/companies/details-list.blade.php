@props(['company'])
<ul class="list-group list-group-flush mb-3">
    <x-ui.info-dropdown-item label="ლიდერები" icon="bi bi-person-lines-fill" name="company_leaders_dropdown"
        :items="$company->users" :getItemText="fn($user) => $user->full_name" />

    <x-ui.info-dropdown-item label="ფილიალები" icon="bi bi-diagram-3" name="company_branches_dropdown"
        :items="$company->branches" :getItemText="fn($branch) => $branch->name" />
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>ეკონომიკური საქ ტიპი:</span>

        @if ($company->economic_activity_type)
            <span class="badge bg-primary rounded-pill">
                {{ $company->economic_activity_type->name }}
            </span>
        @else
            <span class="badge bg-secondary rounded-pill">
                არ არის მითითებული
            </span>
        @endif

    </li>
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>საიდენთიფიკაციო კოდი:</span>
        <span class="badge bg-primary rounded-pill">
            {{ $company->identification_code }}
        </span>
    </li>
</ul>