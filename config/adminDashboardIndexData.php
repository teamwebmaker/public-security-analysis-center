<?php

use App\Models\Branch;
use App\Models\Company;
use App\Models\Contact;
use App\Models\DocumentTemplate;
use App\Models\Info;
use App\Models\Instruction;
use App\Models\MainMenu;
use App\Models\Mentor;
use App\Models\Partner;
use App\Models\Program;
use App\Models\Project;
use App\Models\Publication;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Syllabus;
use App\Models\Task;

return [
   'web_content' => [
      [
         'label' => 'სერვისებთან კავშირში',
         'icon' => 'bi-layers',
         'resources' => [
            'services' => ['title' => 'სერვისები', 'icon' => 'bi-tools', 'model' => Service::class],
            'service_categories' => ['title' => 'სერვის კატეგორიები', 'icon' => 'bi-ui-radios-grid', 'model' => ServiceCategory::class],
            'tasks' => ['title' => 'სამუშაოები', 'icon' => 'bi-card-list', 'model' => Task::class],
         ]
      ],
      [
         'label' => 'სპეც.პროგრამებთან კავშირში',
         'icon' => 'bi-book',
         'resources' => [
            'programs' => ['title' => 'პროგრამები', 'icon' => 'bi-briefcase', 'model' => Program::class],
            'mentors' => ['title' => 'მენტორები', 'icon' => 'bi-person-vcard', 'model' => Mentor::class],
            'syllabuses' => ['title' => 'სილაბუსები', 'icon' => 'bi-file-earmark-text', 'model' => Syllabus::class],


         ]
      ],
      [
         'resources' => [
            'contacts' => ['title' => 'შეტყობინებები', 'icon' => 'bi-mailbox-flag', 'hasCreate' => false, 'model' => Contact::class],
            'projects' => ['title' => 'პროექტები', 'icon' => 'bi-folder-plus', 'model' => Project::class],
            'publications' => ['title' => 'პუბლიკაციები', 'icon' => 'bi-book', 'model' => Publication::class],
            'partners' => ['title' => 'პარტნიორები', 'icon' => 'bi-people', 'model' => Partner::class],
            'infos' => ['title' => 'ჩვენს შესახებ', 'icon' => 'bi-info-circle', 'hasCreate' => false, 'model' => Info::class],
            'main_menus' => ['title' => 'მენიუ', 'icon' => 'bi-menu-button', 'model' => MainMenu::class],
         ]
      ],
   ],
   'management' => [
      [
         'resources' => [
            'companies' => ['title' => 'კომპანიები', 'icon' => 'bi-building', 'model' => Company::class],
            'branches' => ['title' => 'ფილიალები', 'icon' => 'bi-diagram-3', 'model' => Branch::class],
            'tasks' => ['title' => 'სამუშაოები', 'icon' => 'bi-card-list', 'model' => Task::class],
         ]
      ]
   ],
   'resources' => [
      [
         'resources' => [
            'instructions' => ['title' => 'ინსტრუქტაჟები', 'icon' => 'bi-person-video3', 'model' => Instruction::class],
            'document-templates' => ['title' => 'შაბლონები', 'icon' => 'bi-file-earmark-richtext', 'model' => DocumentTemplate::class],
         ]
      ]
   ],
];

