<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminGuard
{
    /**
     * Rout protector from none admin and unauthorized users
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login.page'); // User is not logged in
        }

        if (Auth::user()->isAdmin()) {
            return $next($request); // User is admin, proceed
        }

        // User is logged in but not admin → redirect to admin login
        return redirect()->route('admin.login.page');
    }
}
