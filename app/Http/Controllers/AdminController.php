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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public object $user;

    /**
     * Display admin login form or redirect to dashboard
     */
    public function login()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route("admin.dashboard.page");
        }
        return view("admin.login");
    }

    public function auth(Request $request)
    {
        // Validate request (auto-redirects with errors if failed)
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Get all admin users
        $user = User::where('email', $request->email)
            ->whereHas('role', fn($q) => $q->where('name', 'admin'))
            ->first();


        // Authentication check
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'error' => 'პაროლი ან ელ.ფოსტა არასწორია'
            ]);
            // ]);
        }

        Auth::login($user); // Login user
        $request->session()->regenerate(); // Regenerate session for security
        return redirect()->route('admin.dashboard.page');
    }

    /**
     * Display admin dashboard page with all necessary data
     */
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
        return view("admin.dashboard.index", $data->all());
    }

    /**
     * Logout admin
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route("admin.login.page");
    }
}
