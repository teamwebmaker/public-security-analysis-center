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
use App\Models\User;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public object $user;
    public function login()
    {
        if (Session::has("admin")) {
            return redirect()->route("admin.dashboard.page");
        }
        return view("admin.login");
    }

    public function auth(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('email', 'ელ.ფოსტა არასწორია');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('password', 'პაროლი არასწორია');
        }

        Session::put('admin', $user);

        return redirect()->route('admin.dashboard.page');
    }


    public function dashboard()
    {
        $resources = [
            // services
            'services' => ['title' => 'სერვისები', 'model' => Service::class],
            'service_categories' => ['title' => 'სერვის კატეგორიები', 'model' => ServiceCategory::class],

            // program
            'programs' => ['title' => 'პროგრამები', 'model' => Program::class],
            'syllabuses' => ['title' => 'სილაბუსები', 'model' => Syllabus::class],
            'mentors' => ['title' => 'მენტორები', 'model' => Mentor::class],

            'projects' => ['title' => 'პროექტები', 'model' => Project::class],
            'partners' => ['title' => 'პარტნიორები', 'model' => Partner::class],
            'publications' => ['title' => 'პუბლიკაციები', 'model' => Publication::class],

            'contacts' => ['title' => 'შეტყობინებები', 'model' => Contact::class],
            'infos' => ['title' => 'ჩვენს შესახებ', 'model' => Info::class],
            'main_menus' => ['title' => 'მენიუ', 'model' => MainMenu::class],
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
