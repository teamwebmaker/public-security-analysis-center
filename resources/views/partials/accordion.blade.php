@php
    $accordionItems = [
        [
            'id' => 'contacts',
            'icon' => 'bi-mailbox2-flag',
            'label' => 'შეტყობინებები',
            'routes' => [
                ['name' => 'contacts.index', 'label' => 'პროექტების ნახვა', 'icon' => 'bi-list-ul'],
            ],
            'active-routes' => ['contacts.index',],
        ],
        [
            'id' => 'projects',
            'icon' => 'bi-folder-plus',
            'label' => 'პროექტები',
            'routes' => [
                ['name' => 'projects.index', 'label' => 'პროექტების ნახვა', 'icon' => 'bi-list-ul'],
                ['name' => 'projects.create', 'label' => 'პროექტის შექმნა', 'icon' => 'bi-plus-circle'],
            ]
        ],
        [
            'id' => 'partners',
            'icon' => 'bi-people',
            'label' => 'პარტნიორები',
            'routes' => [
                ['name' => 'partners.index', 'label' => 'პარტნიორების ნახვა', 'icon' => 'bi-list-ul'],
                ['name' => 'partners.create', 'label' => 'პარტნიორის შექმნა', 'icon' => 'bi-plus-circle'],
            ]
        ],
        [
            'id' => 'programs',
            'icon' => 'bi-briefcase',
            'label' => 'სპეც.პროგრამები',
            'routes' => [
                ['name' => 'programs.index', 'label' => 'სპეც.პროგრამების ნახვა', 'icon' => 'bi-list-ul'],
                ['name' => 'programs.create', 'label' => 'სპეც.პროგრამის შექმნა', 'icon' => 'bi-plus-circle'],
                ['name' => 'mentors.index', 'label' => 'მენტორების ნახვა', 'icon' => 'bi-person-workspace'],
                ['name' => 'mentors.create', 'label' => 'მენტორის შექმნა', 'icon' => 'bi-person-plus-fill'],
                ['name' => 'syllabuses.index', 'label' => 'სილაბუსის ნახვა', 'icon' => 'bi-file-earmark-pdf-fill'],
                ['name' => 'syllabuses.create', 'label' => 'სილაბუსის შექმნა', 'icon' => 'bi-file-earmark-plus-fill'],
            ]
        ],
        [
            'id' => 'publications',
            'icon' => 'bi-book',
            'label' => 'პუბლიკაციები',
            'routes' => [
                ['name' => 'publications.index', 'label' => 'პუბლიკაციების ნახვა', 'icon' => 'bi-list-ul'],
                ['name' => 'publications.create', 'label' => 'პუბლიკაციის შექმნა', 'icon' => 'bi-plus-circle'],
            ]
        ],
        [
            'id' => 'service_categories',
            'icon' => 'bi-tools',
            'label' => 'სერვისები',
            'routes' => [
                ['name' => 'services.index', 'label' => 'სერვისები', 'icon' => 'bi-list-ul'],
                ['name' => 'services.create', 'label' => 'სერვისის შექმნა', 'icon' => 'bi-plus-circle'],
                ['name' => 'service_categories.index', 'label' => 'სერვის კატეგორიები', 'icon' => 'bi-list-ul'],
                ['name' => 'service_categories.create', 'label' => 'სერვის კატეგორიის შექმნა', 'icon' => 'bi-plus-circle'],
            ]
        ],
        [
            'id' => 'infos',
            'icon' => 'bi-person-vcard',
            'label' => 'ჩვენს შესახებ',
            'routes' => [
                ['name' => 'infos.index', 'label' => 'რედაქტირება', 'icon' => 'bi-pencil-square'],
            ],
        ],
        [
            'id' => 'main_menus',
            'icon' => 'bi-segmented-nav',
            'label' => 'მენიუ',
            'routes' => [
                ['name' => 'main_menus.index', 'label' => 'სერვისები', 'icon' => 'bi-list-ul'],
                ['name' => 'main_menus.create', 'label' => 'სერვისის შექმნა', 'icon' => 'bi-plus-circle'],
            ]
        ],
    ];
@endphp

<div class="accordion" id="dashboard">
    @foreach ($accordionItems as $item)
        <x-accordion-item :id="$item['id']" :icon="$item['icon']" :label="$item['label']" :routes="$item['routes']" {{--
            Adds .edit automatically to index and edit routes --}}
            :active-routes="collect($item['routes'])->pluck('name')->merge(collect($item['routes'])->pluck('name')->map(fn($r) => str_replace('.index', '.edit', $r)))->unique()->toArray()" />
    @endforeach
</div>

{{--
=== example of what we are rendering :
<x-accordion-item id="publications" icon="bi-book" label="პუბლიკაციები"
    :active-routes="['publications.index', 'publications.create','publications.edit']" :routes="[
        ['name' => 'publications.index', 'label' => 'პუბლიკაციების ნახვა', 'icon' => 'bi-list-ul'],
        ['name' => 'publications.create', 'label' => 'პუბლიკაციის შექმნა', 'icon' => 'bi-plus-circle'],
    ]" /> --}}