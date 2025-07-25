<?php

// Management sidebar
$dashboardItem = ['label' => 'პანელი', 'route' => 'management.dashboard.page', 'icon' => 'bi bi-speedometer2'];
$taskItem = ['label' => 'სამუშაოები', 'route' => '#', 'icon' => 'bi bi-list-ul'];
$companyItem = ['label' => 'კომპანიები', 'route' => '#', 'icon' => 'bi bi-buildings'];
$branchItem = ['label' => 'ფილიალები', 'route' => '#', 'icon' => 'bi bi-diagram-3'];
$personItem = ['label' => 'პასუხისმგებელი პირები', 'route' => '#', 'icon' => 'bi bi-person-badge'];

$commonMenu = [$dashboardItem, $taskItem, $companyItem, $branchItem];
return [

   'company-leader' => array_merge($commonMenu, [$personItem]),
   'responsible-person' => $commonMenu,
   'worker' => $commonMenu,
   'admin' => [
      [
         'id' => 'contacts_wrapper',
         'icon' => 'bi-mailbox-flag',
         'label' => 'შეტყობინებები',
         'parent' => 'dashboard',
         'children' => [
            [
               'id' => 'contacts',
               'icon' => 'bi-chat-dots-fill',
               'label' => 'მესიჯები',
               'routes' => [
                  ['name' => 'contacts.index', 'label' => 'პროექტების ნახვა', 'icon' => 'bi-list-ul'],
               ],
            ]
         ],
      ],
      [

         'id' => 'management',
         'icon' => 'bi-building-gear',
         'label' => 'მენეჯმენტი',
         'parent' => 'dashboard',
         'children' => [
            [
               'id' => 'registration',
               'icon' => 'bi-person-fill-gear',
               'label' => 'დარეგისტრირება',
               'routes' => [

                  ['name' => 'users.index', 'label' => 'მომხმარებლებთა სია', 'icon' => 'bi-list-ul'],
                  ['name' => 'users.create', 'label' => 'მომხმარებლის შექმნა', 'icon' => 'bi-plus-circle'],

               ],
            ],
            [
               'id' => 'companies',
               'icon' => 'bi-building',
               'label' => 'კომპანიები',
               'routes' => [
                  ['name' => 'companies.index', 'label' => 'კომპანიების სია', 'icon' => 'bi-list-ul'],
                  ['name' => 'companies.create', 'label' => 'კომპანიის შექმნა', 'icon' => 'bi-plus-circle'],
                  ['name' => 'economic_activities_types.index', 'label' => 'ეკონომიკური საქმიანობის ტიპების სია', 'icon' => 'bi-tags '],
                  ['name' => 'economic_activities_types.create', 'label' => 'ეკონომიკური საქმიანობის ტიპის შექმნა', 'icon' => 'bi-plus-circle'],
               ],
            ],
            [
               'id' => 'branches',
               'icon' => 'bi-diagram-3',
               'label' => 'ფილიალები',
               'routes' => [
                  ['name' => 'branches.index', 'label' => 'ფილიალების სია', 'icon' => 'bi-list-ul'],
                  ['name' => 'branches.create', 'label' => 'ფილიალის შექმნა', 'icon' => 'bi-plus-circle'],

               ],
            ],
         ],
      ],
      [

         'id' => 'website_content',
         'icon' => 'bi-file-richtext',
         'label' => 'ვებგვერდის კონტენტი',
         'parent' => 'dashboard',
         'children' => [
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
         ]
      ]
   ],
];