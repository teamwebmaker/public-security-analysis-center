<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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

    /**
     * Handle admin user authentication
     */
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
        $resourceGroups = config('adminDashboardIndexData');

        // Build structured dashboard data from grouped resources,
        $dashboardData = collect($resourceGroups)->map(function ($groups) {
            return collect($groups)->map(function ($group) {
                $processedResources = collect($group['resources'])->mapWithKeys(function ($item, $key) {
                    $data = (object) [
                        'title' => $item['title'],
                        'icon' => $item['icon'],
                        'count' => $item['model']::count(),
                        'resourceName' => $key,
                        'hasIndex' => $item['hasIndex'] ?? true,
                        'hasCreate' => $item['hasCreate'] ?? true,
                    ];

                    if ($data->hasIndex) {
                        $data->viewRoute = route($key . '.index');
                    }

                    if ($data->hasCreate) {
                        $data->createRoute = route($key . '.create');
                    }

                    return [$key => $data];
                });

                return (object) [
                    'label' => $group['label'] ?? null,
                    'icon' => $group['icon'] ?? null,
                    'resources' => $processedResources,
                ];
            });
        });


        return view('admin.dashboard.index', [
            'dashboardData' => $dashboardData,
        ]);
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
