<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
class RouteGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            $adminRole = \App\Models\Role::where('name', 'admin')->first();

            // Check if the user's role_id matches the admin role's ID
            if ($adminRole && $user->role_id == $adminRole->id) {
                return $next($request); // User is admin, proceed
            }
        }

        // Not an admin, redirect to login
        return redirect()->route('admin.login.page');
    }
}
