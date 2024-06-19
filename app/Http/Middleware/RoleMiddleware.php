<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
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
    public function handle(Request $request, Closure $next)
    {
        // If user is 'admin', allow access
        if ($request->user()->role === UserRole::ADMIN) {
            return $next($request);
        }

        // If user is 'user'
        if ($request->user()->role === UserRole::USER) {
            // If the user is trying to update their own information, allow access
            if ($request->user()->id == $request->route('id')) {
                return $next($request);
            }
        }

        return response()->json([
            'error' => 'You have no rights'
        ], 403);
    }
}
