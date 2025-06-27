<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated and if their role is 'Admin'
        if (Auth::check() && Auth::user()->role == 'Admin') {
            // If they are an admin, allow the request to proceed
            return $next($request);
        }

        // If not an admin, redirect them to the dashboard with an error message
        return redirect('/dashboard')->with('error', 'You do not have permission to access this page.');
    }
}