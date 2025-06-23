<div class="accordion" id="dashboard">
    <x-accordion-item id="projects" icon="bi-folder-plus" label="პროექტები"
        :active-routes="['projects.index', 'projects.create','projects.edit']" :routes="[
        ['name' => 'projects.index', 'label' => 'პროექტების ნახვა', 'icon' => 'bi-list-ul'],
        ['name' => 'projects.create', 'label' => 'პროექტის შექმნა', 'icon' => 'bi-plus-circle'],
    ]" />

    <x-accordion-item id="partners" icon="bi-people" label="პარტნიორები"
        :active-routes="['partners.index', 'partners.create', 'partners.edit']" :routes="[
        ['name' => 'partners.index', 'label' => 'პარტნიორების ნახვა', 'icon' => 'bi-list-ul'],
        ['name' => 'partners.create', 'label' => 'პარტნიორის შექმნა', 'icon' => 'bi-plus-circle'],
    ]" />

    <x-accordion-item id="programs" icon="bi-briefcase" label="სპეც.პროგრამები"
        :active-routes="['programs.index', 'programs.create','programs.edit']" :routes="[
        ['name' => 'programs.index', 'label' => 'სპეც.პროგრამების ნახვა', 'icon' => 'bi-list-ul'],
        ['name' => 'programs.create', 'label' => 'სპეც.პროგრამის შექმნა', 'icon' => 'bi-plus-circle'],
    ]" />

    <x-accordion-item id="publications" icon="bi-book" label="პუბლიკაციები"
        :active-routes="['publications.index', 'publications.create','publications.edit']" :routes="[
        ['name' => 'publications.index', 'label' => 'პუბლიკაციების ნახვა', 'icon' => 'bi-list-ul'],
        ['name' => 'publications.create', 'label' => 'პუბლიკაციის შექმნა', 'icon' => 'bi-plus-circle'],
    ]" />
    <x-accordion-item id="service-categories" icon="bi-tools" label="სერვისები"
        :active-routes="['service-categories.index', 'service-categories.create','service-categories.edit']" :routes="[
        ['name' => 'service-categories.index', 'label' => 'სერვის კატეგორიები', 'icon' => 'bi-list-ul'],
        ['name' => 'service-categories.create', 'label' => 'სერვის კატეგორიის შექმნა', 'icon' => 'bi-plus-circle'],
    ]" />

    {{-- <x-accordion-item id="contacts" icon="bi-envelope" label="შეტყობინებები"
        :active-routes="['contacts.index', 'contacts.show','contacts.edit']" :routes="[
        ['name' => 'contacts.index', 'label' => 'ნახვა', 'icon' => 'bi-list-ul'],
    ]" /> --}}

</div>