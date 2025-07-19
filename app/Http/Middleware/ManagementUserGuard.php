<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ManagementUserGuard
{
    /**
     * Rout protector from admin and unauthorized users
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If no authenticated user, redirect to the login page
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // If the user has a management-related role, redirect to the management dashboard
        if (Auth::user()->isManagementUser()) {
            return $next($request);
        }

        // For any other role, fallback to the login page
        return redirect()->route('login');
    }

}
