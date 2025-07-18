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
        // if user is already logged in
        if (Auth::check()) {
            return redirect()->route('home.page');
        }

        return view('pages.login');
    }

    public function auth(Request $request)
    {
        // Validate request (auto-redirects with errors if failed)
        $request->validate([
            'phone' => 'required|numeric|digits_between:5,20',
            'password' => 'required',
        ]);

        // Find user (except admin) - admin login logic is in adminController 
        $user = User::where('phone', $request->phone)
            ->whereHas('role', fn($q) => $q->where('name', '!=', 'admin'))
            ->first();


        // Manual authentication check
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'error' => 'პაროლი ან ელ.ფოსტა არასწორია',
            ]);
        }

        Auth::login($user);
        return redirect()->route('home.page');
    }



    public function logout()
    {
        Auth::logout();
        return redirect()->route("home.page");
    }
}
