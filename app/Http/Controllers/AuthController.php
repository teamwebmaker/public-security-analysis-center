<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Display login form or Redirect to dashboard
     */
    public function login()
    {
        // Redirect authenticated user based on role
        if (Auth::check()) {
            return redirect()->route('management.dashboard.page');
        }

        // Display login form
        return view('pages.login');
    }

    public function auth(Request $request)
    {
        // Validate credentials
        $request->validate([
            'phone' => 'required|numeric|digits_between:5,20',
            'password' => 'required',
        ]);

        // Attempt to get users except admin
        $user = User::where('phone', $request->phone)
            ->whereHas('role', fn($q) => $q->where('name', '!=', 'admin'))
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'error' => 'პაროლი ან ტელეფონი არასწორია',
            ]);
        }

        Auth::login($user);
        return redirect()->route('management.dashboard.page');
    }

    /**
     * Logout authenticated user
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('home.page');
    }
}
