<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Project;
use App\Models\Publication;
use App\Models\Partner;
use App\Models\Contact;
use App\Models\Info;
use App\Models\MainMenu;
use App\Models\Mentor;
use App\Models\Program;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Syllabus;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public object $user;

    public function __construct()
    {
        $this->user = (object) [
            "email" => "psac@admin.panel",
            "password" =>
                '$2y$10$PlR/Juu2rW/s8yfMMNs29OcEpn6zL2HxcCSCBrSTKiVS07FeotMZO', // $AdminPanel2024#
        ];
    }
    public function login()
    {
        if (Session::has("admin")) {
            return redirect()->route("admin.dashboard.page");
        }
        return view("admin.login");
    }

    public function auth(Request $request)
    {
        if ($this->user->email == $request->email) {
            if (password_verify($request->password, $this->user->password)) {
                Session::put("admin", $this->user);
                return redirect()->route("admin.dashboard.page");
            } else {
                return redirect()
                    ->back()
                    ->with("password", "პაროლი არასწორია");
            }
        } else {
            return redirect()
                ->back()
                ->with("email", "ელ.ფოსტა არასწორია");
        }
    }

    public function dashboard()
    {
        $resources = [
            'projects' => ['title' => 'პროექტები', 'model' => Project::class],
            'contacts' => ['title' => 'შეტყობინებები', 'model' => Contact::class],
            'partners' => ['title' => 'პარტნიორები', 'model' => Partner::class],
            'publications' => ['title' => 'პუბლიკაციები', 'model' => Publication::class],
            'infos' => ['title' => 'ჩვენს შესახებ', 'model' => Info::class],
            'main_menus' => ['title' => 'მენიუ', 'model' => MainMenu::class],
            // program
            'programs' => ['title' => 'პროგრამები', 'model' => Program::class],
            'syllabuses' => ['title' => 'სილაბუსები', 'model' => Syllabus::class],
            'mentors' => ['title' => 'მენტორები', 'model' => Mentor::class],
            // services
            'services' => ['title' => 'სერვისები', 'model' => Service::class],
            'service_categories' => ['title' => 'სერვის კატეგორიები', 'model' => ServiceCategory::class],
        ];

        $data = collect($resources)->mapWithKeys(function ($item, $key) {
            return [
                $key => (object) [
                    'title' => $item['title'],
                    'count' => $item['model']::count(),
                    'resourceName' => $key,
                ]
            ];
        });
        return view("admin.desk", $data->all());
    }

    public function logout()
    {
        Session::forget("admin");
        return redirect()->route("admin.login.page");
    }
}
