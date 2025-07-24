@props(['company'])
<ul class="list-group list-group-flush mb-3">
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>ეკონომიკური საქ ტიპი:</span>
        <span class="badge bg-primary rounded-pill">
            {{ $company->economic_activity_type->display_name }}
        </span>
    </li>
    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
        <span>საიდენთიფიკაციო კოდი:</span>
        <span class="badge bg-primary rounded-pill">
            {{ $company->identification_code }}
        </span>
    </li>
</ul>