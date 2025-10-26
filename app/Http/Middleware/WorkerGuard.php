<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class WorkerGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login'); // User is not logged in
        }

        if (Auth::user()->getRoleName() == 'worker') {
            return $next($request); // User is worker, proceed
        }

        // User is logged in but not worker → redirect to login
        return redirect()->route('login');
    }
}
