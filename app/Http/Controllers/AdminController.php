<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Project;
use App\Models\Publication;
use App\Models\Partner;
use App\Models\Contact;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public object $user;

    public function __construct()
    {
        $this->user = (object) [

            'email' => 'psac@admin.panel',
            'password' => '$2y$10$PlR/Juu2rW/s8yfMMNs29OcEpn6zL2HxcCSCBrSTKiVS07FeotMZO' //$AdminPanel2024#
        ];
    }
    public function login()
    {
        if (Session::has('admin'))
            return redirect()->route('admin.dashboard.page');
        return view('admin.login');
    }

    public function auth(Request $request)
    {
        if ($this->user->email == $request->email) {
            if (password_verify($request->password, $this->user->password)) {
                Session::put('admin', $this->user);
                return redirect()->route('admin.dashboard.page');
            } else
                return redirect()->back()->with('password', 'პაროლი არასწორია');
        } else
            return redirect()->back()->with('email', 'ელ.ფოსტა არასწორია');

    }

    public function dashboard()
    {
        return view('admin.desk', [
            'projects' => (object) [
                'title' => 'პროექტები',
                'count' => Project::count()
            ],
            'publications' => (object) [
                'title' => 'პუბლიკაციები',
                'count' => Publication::count()
            ],
            'partners' => (object) [
                'title' => 'პარტნიორები',
                'count' => Partner::count()
            ],
            'contacts' => (object) [
                'title' => 'შეტყობინებები',
                'count' => Contact::count()
            ]
        ]);
    }

    public function logout()
    {
        Session::forget('admin');
        return redirect()->route('admin.login.page');
    }
}
