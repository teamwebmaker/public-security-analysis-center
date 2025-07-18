<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        // If user is already logged in, redirect based on role
        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user());
        }

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
        return $this->redirectToDashboard($user);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home.page');
    }

    /**
     * Redirect the user based on their role after login.
     */
    private function redirectToDashboard(User $user)
    {
        return redirect()->route('home.page');
        // match ($user->role->name) {
        //     'company_leader' => redirect()->route('company.dashboard'),
        //     'responsible_person' => redirect()->route('responsible.dashboard'),
        //     'worker' => redirect()->route('worker.dashboard'),
        //     default => redirect()->route('home.page'),
        // };
    }
}
