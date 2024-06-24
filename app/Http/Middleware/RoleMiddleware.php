<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next,  $role)
    {
        $user = Auth::user();
        // Check if the user has the required role
        if ($user->role === UserRole::ADMIN && $role === 'admin') {
            return $next($request);
        }

        if ($user->role === UserRole::USER && $role === 'user') {
            // Additional check for user role, if needed
            return $next($request);
        }

        return response()->json([
            'error' => 'You have no rights'
        ], 403);
    }
}
