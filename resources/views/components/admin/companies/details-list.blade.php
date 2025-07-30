@props(['company'])
<ul class="list-group list-group-flush mb-3">
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