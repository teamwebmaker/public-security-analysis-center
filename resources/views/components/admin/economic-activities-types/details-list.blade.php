@props(['companies'])

<ul class="list-group list-group-flush mb-3">
    @if ($companies->isNotEmpty())
        <x-ui.info-dropdown-item label="კომპანიები" icon="bi bi-building" name="companies_dropdown" :items="$companies"
            :getItemText="fn($company) => $company->name" />
    @endif
</ul>